<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:manage-products']);
    }

    /** Lista o estoque com filtros */
    public function index(Request $request)
    {
        $search   = $request->input('search');
        $category = $request->input('category');
        $supplier = $request->input('supplier');

        $query = Inventory::with('product.category', 'product.supplier');

        if ($search) {
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->whereHas('product.category', function ($q) use ($category) {
                $q->where('id', $category);
            });
        }

        if ($supplier) {
            $query->whereHas('product.supplier', function ($q) use ($supplier) {
                $q->where('id', $supplier);
            });
        }

        $inventories = $query
            ->paginate(10)
            ->appends($request->only(['search', 'category', 'supplier']));

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('inventory.index', compact(
            'inventories',
            'categories',
            'suppliers',
            'search',
            'category',
            'supplier'
        ));
    }

    /** Exporta estoque para CSV */
    public function exportCsv(): StreamedResponse
    {
        $fileName = 'estoque_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $columns = ['Produto','Categoria','Fornecedor','Quantidade Atual','Quantidade Ideal'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns, ';');

            Inventory::with('product.category','product.supplier')
                ->chunk(200, function ($inventories) use ($handle) {
                    foreach ($inventories as $inv) {
                        fputcsv($handle, [
                            $inv->product->name,
                            $inv->product->category->name,
                            $inv->product->supplier->name,
                            $inv->qnt_estoque,
                            $inv->qnt_ideal,
                        ], ';');
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** Formulário de edição/criação de snapshot de estoque */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('inventory.create', compact('products'));
    }

    /** Armazena ou atualiza snapshot de estoque */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'qnt_estoque'  => 'required|integer|min:0',
            'qnt_ideal'    => 'required|integer|min:0',
        ]);

        Inventory::updateOrCreate(
            ['product_id' => $validated['product_id']],
            $validated
        );

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Estoque atualizado com sucesso.');
    }

    /** Formulário de edição */
    public function edit(Inventory $inventory)
    {
        $products = Product::orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'products'));
    }

    /** Atualiza snapshot de estoque */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'qnt_estoque'  => 'required|integer|min:0',
            'qnt_ideal'    => 'required|integer|min:0',
        ]);

        $inventory->update($validated);

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Estoque atualizado com sucesso.');
    }

    /** Remove registro de estoque */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Registro de estoque removido com sucesso.');
    }
}