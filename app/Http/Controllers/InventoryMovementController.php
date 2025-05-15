<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-products');
    }

    /** Exibe o formulário de Entrada de Estoque */
    public function createEntry()
    {
        $products = Product::with('supplier')
                           ->orderBy('name')
                           ->get();

        return view('entries.create', compact('products'));
    }

    /** Lista histórico */
    public function index(Request $request)
    {
        $query = InventoryMovement::with(['product', 'responsible'])
                                  ->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $movements = $query->paginate(15);

        return view('movements.index', compact('movements'));
    }

    /** Registra entrada e atualiza estoque */
    public function storeEntry(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes'      => 'nullable|string',
        ]);

        $inventory = Inventory::firstOrNew(['product_id' => $data['product_id']]);
        $before = $inventory->qnt_estoque ?? 0;
        $after = $before + $data['quantity'];

        InventoryMovement::create([
            'product_id'     => $data['product_id'],
            'type'           => 'in',
            'quantity'       => $data['quantity'],
            'unit_price'     => $data['unit_price'],
            'total_price'    => $data['unit_price'] * $data['quantity'],
            'before_stock'   => $before,
            'after_stock'    => $after,
            'responsible_id' => Auth::id(),
            'notes'          => $data['notes'] ?? null,
        ]);

        $inventory->qnt_estoque = $after;
        $inventory->save();

        return back()->with('success', 'Entrada registrada com sucesso.');
    }

    /** Registra saída e atualiza estoque */
    public function storeExit(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        $inventory = Inventory::where('product_id', $data['product_id'])->firstOrFail();
        $before = $inventory->qnt_estoque;
        $after = max(0, $before - $data['quantity']);

        // Busca último preço de entrada
        $lastIn = InventoryMovement::where('product_id', $data['product_id'])
                    ->where('type', 'in')
                    ->latest('created_at')
                    ->first();
        $unitPrice = $lastIn ? $lastIn->unit_price : 0;

        InventoryMovement::create([
            'product_id'     => $data['product_id'],
            'type'           => 'out',
            'quantity'       => $data['quantity'],
            'unit_price'     => $unitPrice,
            'total_price'    => $unitPrice * $data['quantity'],
            'before_stock'   => $before,
            'after_stock'    => $after,
            'responsible_id' => Auth::id(),
            'notes'          => $data['notes'] ?? null,
        ]);

        $inventory->qnt_estoque = $after;
        $inventory->save();

        return back()->with('success', 'Saída registrada com sucesso.');
    }

    /** Exporta Histórico em CSV */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'historico_movimentacoes_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $columns = ['Data','Produto','Qtd','Antes','Depois','Usuário','Tipo','Obs'];

        $callback = function() use ($columns) {
            $handle = fopen('php://output','w');
            fputcsv($handle, $columns, ';');

            InventoryMovement::with('product','responsible')
                ->orderBy('created_at','desc')
                ->chunk(200, function($movements) use ($handle) {
                    foreach ($movements as $m) {
                        fputcsv($handle, [
                            $m->created_at->format('d/m/Y H:i'),
                            $m->product->name,
                            $m->quantity,
                            $m->before_stock,
                            $m->after_stock,
                            $m->responsible->name,
                            ucfirst($m->type),
                            $m->notes,
                        ], ';');
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}