<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductItem;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $search = $request->input('search');

        $products = Product::query()
            ->with(['category', 'supplier', 'inventory']) // evita N+1
            ->withCount([
                // conta apenas AVAILABLE p/ não-consumíveis (usado no accessor)
                'items as available_items_count' => function ($q) {
                    $q->where('status', \App\Models\ProductItem::STATUS_AVAILABLE);
                },
            ])
            ->when($search, fn ($q) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate(10)
            ->appends(['search' => $search]);

        return view('products.index', compact('products', 'search'));
    }

    // ... (demais métodos permanecem iguais)
    
    public function create()
    {
        $this->authorize('create', Product::class);

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $avgCost = $this->normalizeMoney($request->input('price_custo', '0'));

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category_id'  => 'required|exists:categories,id',
            'supplier_id'  => 'required|exists:suppliers,id',
            'min_stock'    => 'nullable|integer|min:0',
            'initial_qty'  => 'nullable|integer|min:0',
            'is_consumable'=> 'nullable|boolean',
            'image'        => 'nullable|image|max:5120|mimes:jpg,jpeg,png,webp,tiff',
        ]);

        $payload = [
            'name'         => $data['name'],
            'description'  => $data['description'] ?? null,
            'category_id'  => $data['category_id'],
            'supplier_id'  => $data['supplier_id'],
            'avg_cost'     => $avgCost,
            'min_stock'    => $data['min_stock'] ?? 0,
            'is_consumable'=> (bool)($data['is_consumable'] ?? false),
        ];

        if ($request->hasFile('image')) {
            $payload['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($payload);

        // Quantidade inicial
        $initialQty = (int) ($data['initial_qty'] ?? 0);
        if ($initialQty > 0) {
            DB::transaction(function () use ($product, $initialQty) {
                if ($product->is_consumable) {
                    $inv = Inventory::firstOrCreate(
                        ['product_id' => $product->id],
                        ['qnt_estoque' => 0, 'qnt_ideal' => 0]
                    );
                    $inv->increment('qnt_estoque', $initialQty);
                } else {
                    for ($i = 0; $i < $initialQty; $i++) {
                        ProductItem::create([
                            'product_id'        => $product->id,
                            'serial_internal'   => null,
                            'serial_external'   => null,
                            'status'            => ProductItem::STATUS_AVAILABLE,
                            'current_holder_id' => null,
                            'acquired_at'       => now(),
                            'notes'             => 'Entrada inicial pelo cadastro do produto',
                        ]);
                    }
                }
            });
        }

        return redirect()->route('products.index')->with('success', 'Produto criado com sucesso!');
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $this->authorize('update', $product);

        $avgCost = $this->normalizeMoney($request->input('price_custo', (string)($product->avg_cost ?? 0)));

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category_id'  => 'required|exists:categories,id',
            'supplier_id'  => 'required|exists:suppliers,id',
            'min_stock'    => 'nullable|integer|min:0',
            'initial_qty'  => 'nullable|integer|min:0',
            'is_consumable'=> 'nullable|boolean',
            'image'        => 'nullable|image|max:5120|mimes:jpg,jpeg,png,webp,tiff',
        ]);

        $payload = [
            'name'         => $data['name'],
            'description'  => $data['description'] ?? null,
            'category_id'  => $data['category_id'],
            'supplier_id'  => $data['supplier_id'],
            'avg_cost'     => $avgCost,
            'min_stock'    => $data['min_stock'] ?? ($product->min_stock ?? 0),
            'is_consumable'=> (bool)($data['is_consumable'] ?? false),
        ];

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $payload['image_path'] = $request->file('image')->store('products', 'public');
        } else {
            $payload['image_path'] = $product->image_path;
        }

        $product->update($payload);

        $extra = (int) ($data['initial_qty'] ?? 0);
        if ($extra > 0) {
            DB::transaction(function () use ($product, $extra) {
                if ($product->is_consumable) {
                    $inv = Inventory::firstOrCreate(
                        ['product_id' => $product->id],
                        ['qnt_estoque' => 0, 'qnt_ideal' => 0]
                    );
                    $inv->increment('qnt_estoque', $extra);
                } else {
                    for ($i = 0; $i < $extra; $i++) {
                        ProductItem::create([
                            'product_id'        => $product->id,
                            'serial_internal'   => null,
                            'serial_external'   => null,
                            'status'            => ProductItem::STATUS_AVAILABLE,
                            'current_holder_id' => null,
                            'acquired_at'       => now(),
                            'notes'             => 'Itens adicionados via edição do produto',
                        ]);
                    }
                }
            });
        }

        return redirect()->route('products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produto excluído com sucesso.');
    }

    public function removeImage(Product $product)
    {
        $this->authorize('update', $product);

        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
            $product->image_path = null;
            $product->save();
        }
        return back()->with('success', 'Foto removida com sucesso!');
    }

    /** "R$ 1.234,56" -> 1234.56 */
    private function normalizeMoney(?string $raw): float
    {
        if ($raw === null) return 0.0;
        $raw = trim($raw);
        $raw = str_ireplace(['R$', ' '], '', $raw);
        $raw = str_replace('.', '', $raw);
        $raw = str_replace(',', '.', $raw);
        return (float) $raw;
    }
}
