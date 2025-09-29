<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /** Lista o estoque com filtros */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Inventory::class);

        $search   = $request->input('search');
        $category = $request->input('category');
        $supplier = $request->input('supplier');

        $query = Inventory::with(['product.category', 'product.supplier']);

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
            ->orderByDesc('id')
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
        $this->authorize('viewAny', Inventory::class);

        $fileName = 'estoque_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $columns = ['Produto', 'Categoria', 'Fornecedor', 'Quantidade Atual', 'Quantidade Ideal'];

        $callback = function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns, ';');

            Inventory::with('product.category', 'product.supplier')
                ->chunk(200, function ($inventories) use ($handle) {
                    foreach ($inventories as $inv) {
                        fputcsv($handle, [
                            optional($inv->product)->name,
                            optional(optional($inv->product)->category)->name,
                            optional(optional($inv->product)->supplier)->name,
                            $inv->qnt_estoque,
                            $inv->qnt_ideal,
                        ], ';');
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** Snapshot manual */
    public function create()
    {
        $this->authorize('create', Inventory::class);
        $products = Product::orderBy('name')->get();
        return view('inventory.create', compact('products'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Inventory::class);

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

    public function edit(Inventory $inventory)
    {
        $this->authorize('update', $inventory);
        $products = Product::orderBy('name')->get();
        return view('inventory.edit', compact('inventory', 'products'));
    }

    public function update(Request $request, Inventory $inventory)
    {
        $this->authorize('update', $inventory);

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

    public function destroy(Inventory $inventory)
    {
        $this->authorize('delete', $inventory);
        $inventory->delete();

        return redirect()
            ->route('inventory.index')
            ->with('success', 'Registro de estoque removido com sucesso.');
    }

    /** Itens individuais do Inventory (rota: inventory/{inventory}/items) */
    public function items(Inventory $inventory)
    {
        $this->authorize('viewAny', Inventory::class);

        $product = $inventory->product()->with('category', 'supplier')->firstOrFail();

        $items = ProductItem::with('holder')
            ->where('product_id', $product->id)
            ->orderByDesc('id')
            ->paginate(20);

        $stats = [
            'available' => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_AVAILABLE)->count(),
            'loaned'    => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_LOANED)->count(),
            'consumed'  => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_CONSUMED)->count(),
            'broken'    => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_BROKEN)->count(),
            'lost'      => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_LOST)->count(),
        ];

        return view('inventory.items', compact('product', 'items', 'stats'));
    }

    /** Parcial em HTML para o accordion (usada hoje na listagem) */
    public function itemsPartial(Product $product)
    {
        $this->authorize('viewAny', Inventory::class);

        $items = $product->items()
            ->with('holder')
            ->orderByDesc('id')
            ->get();

        $users = User::orderBy('name')->get();

        return view('inventory.partials.items_table', compact('product', 'items', 'users'));
    }

    /**
     * NOVO: JSON de itens por produto (rota: inventory/{product}/items.json)
     * Usado para carregar a sanfona sob demanda via fetch/AJAX.
     */
    public function itemsJson(Product $product): JsonResponse
    {
        $this->authorize('viewAny', Inventory::class);

        // Dados básicos do produto
        $prod = $product->load(['category:id,name', 'supplier:id,name']);

        // Lista de itens
        $items = ProductItem::with('holder:id,name')
            ->where('product_id', $product->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (ProductItem $i) {
                return [
                    'id'              => $i->id,
                    'serial_internal' => $i->serial_internal,
                    'serial_external' => $i->serial_external,
                    'status'          => $i->status,
                    'status_label'    => match ($i->status) {
                        ProductItem::STATUS_AVAILABLE => 'Disponível',
                        ProductItem::STATUS_LOANED    => 'Emprestado',
                        ProductItem::STATUS_CONSUMED  => 'Consumido',
                        ProductItem::STATUS_BROKEN    => 'Quebrado',
                        ProductItem::STATUS_LOST      => 'Perdido',
                        default                        => $i->status,
                    },
                    'acquired_at'     => optional($i->acquired_at)->format('d/m/Y'),
                    'holder'          => $i->holder ? [
                        'id'   => $i->holder->id,
                        'name' => $i->holder->name,
                    ] : null,
                    'can_loan'        => $i->status === ProductItem::STATUS_AVAILABLE && !$i->product?->is_consumable,
                ];
            });

        // Estatísticas rápidas
        $stats = [
            'available' => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_AVAILABLE)->count(),
            'loaned'    => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_LOANED)->count(),
            'consumed'  => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_CONSUMED)->count(),
            'broken'    => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_BROKEN)->count(),
            'lost'      => ProductItem::where('product_id', $product->id)->where('status', ProductItem::STATUS_LOST)->count(),
        ];

        return response()->json([
            'product' => [
                'id'         => $prod->id,
                'name'       => $prod->name,
                'category'   => $prod->category?->name,
                'supplier'   => $prod->supplier?->name,
                'is_consumable' => (bool) ($prod->is_consumable ?? false),
            ],
            'items'  => $items,
            'stats'  => $stats,
        ]);
    }
}
