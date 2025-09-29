<?php

namespace App\Http\Controllers;

use App\Models\InventoryMovement;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryMovementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /* ============ VIEWS ============ */

    public function createEntry()
    {
        $this->authorize('create', InventoryMovement::class);
        $products = Product::with('supplier')->orderBy('name')->get();
        return view('entries.create', compact('products'));
    }

    public function timeline()
    {
        $this->authorize('viewAny', InventoryMovement::class);
        $movements = InventoryMovement::with(['product', 'item', 'collaborator', 'performer'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('movements.timeline', compact('movements'));
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', InventoryMovement::class);

        $query = InventoryMovement::with(['product', 'item', 'collaborator', 'performer'])->latest();

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $movements = $query->paginate(15);
        return view('movements.index', compact('movements'));
    }

    /* ============ ENTRADA (lote) ============ */
    public function storeEntry(Request $request)
    {
        $this->authorize('create', InventoryMovement::class);

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes'      => 'nullable|string|max:500',
        ]);

        $qty      = (int) $data['quantity'];
        $unitCost = (float) $data['unit_price'];
        $notes    = $data['notes'] ?? null;

        $product = Product::findOrFail($data['product_id']);

        DB::transaction(function () use ($qty, $unitCost, $notes, $product) {
            if ($product->is_consumable) {
                // Consumíveis -> altera snapshot
                $inv = Inventory::firstOrCreate(
                    ['product_id' => $product->id],
                    ['qnt_estoque' => 0, 'qnt_ideal' => 0]
                );
                // trava a linha para concorrência
                $inv = Inventory::where('product_id', $product->id)->lockForUpdate()->first();
                $before = (int) $inv->getRawOriginal('qnt_estoque');
                $after  = $before + $qty;
                $inv->update(['qnt_estoque' => $after]);
            } else {
                // Não-consumíveis -> cria N itens AVAILABLE
                $before = (int) $product->items()->where('status', ProductItem::STATUS_AVAILABLE)->count();
                $after  = $before + $qty;

                for ($i = 0; $i < $qty; $i++) {
                    ProductItem::create([
                        'product_id'        => $product->id,
                        'serial_internal'   => null, // gerado no boot()
                        'serial_external'   => null,
                        'status'            => ProductItem::STATUS_AVAILABLE,
                        'current_holder_id' => null,
                        'acquired_at'       => now(),
                        'notes'             => $notes,
                    ]);
                }
            }

            InventoryMovement::create([
                'product_id'      => $product->id,
                'product_item_id' => null,
                'type'            => InventoryMovement::TYPE_ENTRY,
                'qty'             => $qty,
                'quantity'  => $qty, // compat
                'before_stock'    => $before,
                'after_stock'     => $after,
                'unit_cost'       => $unitCost,
                'total_cost'      => $unitCost * $qty,
                'collaborator_id' => null,
                'performed_by'    => Auth::id(),
                'performed_at'    => now(),
                'notes'           => $notes,
                'properties'      => ['source' => 'manual_entry'],
            ]);

            // Atualiza custo médio ponderado (usa AVAILABLE atuais como base)
            $currentQty  = $before;
            $currentCost = (float) ($product->avg_cost ?? 0);
            $newQty      = $currentQty + $qty;
            if ($newQty > 0) {
                $newAvg = (($currentQty * $currentCost) + ($qty * $unitCost)) / $newQty;
                $product->avg_cost = round($newAvg, 2);
                $product->save();
            }
        });

        return back()->with('success', 'Entrada registrada com sucesso.');
    }

    /* ============ SAÍDA em lote (consumíveis) ============ */
    public function storeExit(Request $request)
    {
        $this->authorize('create', InventoryMovement::class);

        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($data['product_id']);
        if (!$product->is_consumable) {
            return back()->with('error', 'Para itens não-consumíveis, use Empréstimo/Devolução por item.');
        }

        DB::transaction(function () use ($data, $product) {
            $inv    = Inventory::where('product_id', $product->id)->lockForUpdate()->firstOrFail();
            $before = (int) $inv->getRawOriginal('qnt_estoque');
            $qty    = min($before, (int) $data['quantity']);
            $after  = max(0, $before - $qty);

            $inv->update(['qnt_estoque' => $after]);

            InventoryMovement::create([
                'product_id'      => $product->id,
                'product_item_id' => null,
                'type'            => InventoryMovement::TYPE_CONSUMPTION,
                'qty'             => $qty,
                'quantity'  => $qty, // compat
                'before_stock'    => $before,
                'after_stock'     => $after,
                'unit_cost'       => $product->avg_cost ?? 0,
                'total_cost'      => ($product->avg_cost ?? 0) * $qty,
                'collaborator_id' => null,
                'performed_by'    => Auth::id(),
                'performed_at'    => now(),
                'notes'           => $data['notes'] ?? null,
                'properties'      => ['mode' => 'bulk_consumable'],
            ]);
        });

        return back()->with('success', 'Saída (consumo) registrada.');
    }

    /* ============ EMPRÉSTIMO (por item) ============ */
    public function loanOut(Request $request, ProductItem $item)
    {
        $this->authorize('create', InventoryMovement::class);

        $data = $request->validate([
            'collaborator_id' => 'required|exists:users,id',
            'notes'           => 'nullable|string|max:500',
        ]);

        // Controle de concorrência: trava a linha do item
        DB::transaction(function () use ($data, $item) {
            $item = ProductItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $product = $item->product()->first();

            if ($product->is_consumable) {
                abort(400, 'Itens consumíveis não podem ser emprestados.');
            }
            if ($item->status !== ProductItem::STATUS_AVAILABLE) {
                abort(400, 'Item não está disponível para empréstimo.');
            }

            $before = (int) $product->items()->where('status', ProductItem::STATUS_AVAILABLE)->count();
            $after  = max(0, $before - 1);

InventoryMovement::create([
    'product_id'      => $product->id,
    'product_item_id' => $item->id,
    'type'            => InventoryMovement::TYPE_LOAN_OUT,
    'qty'             => 1,
    'quantity'        => 1, // compat
    'before_stock'    => $before,
    'after_stock'     => $after,
    'unit_cost'       => $product->avg_cost ?? 0,
    'total_cost'      => $product->avg_cost ?? 0,
    'collaborator_id' => $data['collaborator_id'],
    'performed_by'    => Auth::id(),
    'performed_at'    => now(),
    'notes'           => $data['notes'] ?? null,
    'properties'      => ['action' => 'loan_out'],
            ]);

            $item->status = ProductItem::STATUS_LOANED;
            $item->current_holder_id = (int) $data['collaborator_id'];
            $item->save();
        });

        return back()->with('success', 'Empréstimo registrado.');
    }

    /* ============ DEVOLUÇÃO (por item) ============ */
    public function loanReturn(Request $request, ProductItem $item)
    {
        $this->authorize('create', InventoryMovement::class);

        DB::transaction(function () use ($item) {
            $item = ProductItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $product = $item->product()->first();

            if ($item->status !== ProductItem::STATUS_LOANED) {
                abort(400, 'Este item não está emprestado.');
            }

            $before = (int) $product->items()->where('status', ProductItem::STATUS_AVAILABLE)->count();
            $after  = $before + 1;

            InventoryMovement::create([
                'product_id'      => $product->id,
                'product_item_id' => $item->id,
                'type'            => InventoryMovement::TYPE_LOAN_RETURN,
                'qty'             => 1,
                'quantity'  => 1, // compat
                'before_stock'    => $before,
                'after_stock'     => $after,
                'unit_cost'       => $product->avg_cost ?? ($product->unit_price ?? 0),
                'total_cost'      => $product->avg_cost ?? ($product->unit_price ?? 0),
                'collaborator_id' => $item->current_holder_id,
                'performed_by'    => Auth::id(),
                'performed_at'    => now(),
                'notes'           => null,
                'properties'      => ['action' => 'loan_return'],
            ]);

            $item->status = ProductItem::STATUS_AVAILABLE;
            $item->current_holder_id = null;
            $item->save();
        });

        return back()->with('success', 'Devolução registrada.');
    }

    /* ============ CONSUMO (por item não-consumível) ============ */
    public function consume(Request $request, ProductItem $item)
    {
        $this->authorize('create', InventoryMovement::class);

        $data = $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($item, $data) {
            $item = ProductItem::whereKey($item->id)->lockForUpdate()->firstOrFail();
            $product = $item->product()->first();

            if ($product->is_consumable) {
                abort(400, 'Para consumíveis use a saída em lote.');
            }

            $before = (int) $product->items()->where('status', ProductItem::STATUS_AVAILABLE)->count();
            $delta  = ($item->status === ProductItem::STATUS_AVAILABLE) ? 1 : 0;
            $after  = $before - $delta;

            InventoryMovement::create([
                'product_id'      => $product->id,
                'product_item_id' => $item->id,
                'type'            => InventoryMovement::TYPE_CONSUMPTION,
                'qty'             => 1,
                'quantity'  => 1, // compat
                'before_stock'    => $before,
                'after_stock'     => $after,
                'unit_cost'       => $product->avg_cost ?? ($product->unit_price ?? 0),
                'total_cost'      => $product->avg_cost ?? ($product->unit_price ?? 0),
                'collaborator_id' => $item->current_holder_id,
                'performed_by'    => Auth::id(),
                'performed_at'    => now(),
                'notes'           => $data['notes'] ?? null,
                'properties'      => ['mode' => 'single_item'],
            ]);

            $item->status = ProductItem::STATUS_CONSUMED;
            $item->current_holder_id = null;
            $item->save();
        });

        return back()->with('success', 'Consumo registrado para o item.');
    }

    /* ============ AJUSTE ============ */
    public function adjust(Request $request, ProductItem $item)
    {
        $this->authorize('create', InventoryMovement::class);

        $data = $request->validate([
            'status'          => 'required|string|in:AVAILABLE,LOANED,CONSUMED,BROKEN,LOST',
            'serial_external' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:500',
        ]);

        if ($data['status'] === ProductItem::STATUS_LOANED) {
            return back()->with('error', 'Para marcar LOANED utilize a ação de Empréstimo.');
        }

        $item->serial_external   = $data['serial_external'] ?? $item->serial_external;
        $item->notes             = $data['notes'] ?? $item->notes;
        $item->current_holder_id = $data['status'] === ProductItem::STATUS_AVAILABLE ? null : $item->current_holder_id;
        $item->status            = $data['status'];
        $item->save();

        InventoryMovement::create([
            'product_id'      => $item->product_id,
            'product_item_id' => $item->id,
            'type'            => InventoryMovement::TYPE_ADJUST,
            'qty'             => 0,
            'quantity'  => 0, // compat
            'before_stock'    => null,
            'after_stock'     => null,
            'unit_cost'       => null,
            'total_cost'      => null,
            'collaborator_id' => null,
            'performed_by'    => Auth::id(),
            'performed_at'    => now(),
            'notes'           => $data['notes'] ?? 'Ajuste de item',
            'properties'      => ['status' => $data['status']],
        ]);

        return back()->with('success', 'Item ajustado.');
    }

    /* ============ EXPORT CSV ============ */
    public function exportCsv(): StreamedResponse
    {
        $this->authorize('viewAny', InventoryMovement::class);

        $fileName = 'historico_movimentacoes_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $columns = ['Data','Produto','Serial','Qtd','Antes','Depois','Tipo','Colaborador','Executor','Obs'];

        $callback = function() use ($columns) {
            $handle = fopen('php://output','w');
            fputcsv($handle, $columns, ';');

            InventoryMovement::with(['product','item','collaborator','performer'])
                ->orderBy('created_at','desc')
                ->chunk(300, function($movements) use ($handle) {
                    foreach ($movements as $m) {
                        $typeLabel = match ($m->type) {
                            InventoryMovement::TYPE_ENTRY       => 'Entrada',
                            InventoryMovement::TYPE_LOAN_OUT    => 'Empréstimo',
                            InventoryMovement::TYPE_LOAN_RETURN => 'Devolução',
                            InventoryMovement::TYPE_CONSUMPTION => 'Consumo',
                            InventoryMovement::TYPE_ADJUST      => 'Ajuste',
                            default                             => $m->type,
                        };

                        fputcsv($handle, [
                            optional($m->created_at)->format('d/m/Y H:i'),
                            optional($m->product)->name,
                            optional($m->item)->serial_internal,
                            $m->qty,
                            $m->before_stock,
                            $m->after_stock,
                            $typeLabel,
                            optional($m->collaborator)->name,
                            optional($m->performer)->name,
                            $m->notes,
                        ], ';');
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
