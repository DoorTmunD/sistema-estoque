<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-products');
    }

    /**
     * Lista paginada de produtos.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Product::with('category', 'supplier');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $products = $query->orderBy('name')
                          ->paginate(10)
                          ->appends(['search' => $search]);

        return view('products.index', compact('products', 'search'));
    }

    /**
     * Formulário de criação.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $suppliers  = Supplier::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    /**
     * Armazena um novo produto e registra entrada inicial no estoque.
     */
public function store(Request $request)
{
    // sanitiza unit_price como antes...
    $rawPrice = $request->input('unit_price', '0');
    $sanitizedPrice = str_replace(['R$ ', '.', ','], ['', '', '.'], $rawPrice);
    $request->merge(['unit_price' => $sanitizedPrice]);

    // valida agora 'quantity' em vez de 'initial_quantity'
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'supplier_id' => 'required|exists:suppliers,id',
        'quantity'    => 'required|integer|min:1',    // <--- aqui
        'unit_price'  => 'required|numeric|min:0',
    ]);

    // cria o produto incluindo 'quantity'
    $product = Product::create([
        'name'        => $data['name'],
        'description' => $data['description'] ?? null,
        'category_id' => $data['category_id'],
        'supplier_id' => $data['supplier_id'],
        'quantity'    => $data['quantity'],          // <--- aqui
        'unit_price'  => $data['unit_price'],
    ]);

    // registra movimentação de entrada
    InventoryMovement::create([
        'product_id'     => $product->id,
        'type'           => 'in',
        'quantity'       => $data['quantity'],
        'unit_price'     => $data['unit_price'],
        'total_price'    => $data['quantity'] * $data['unit_price'],
        'responsible_id' => Auth::id(),
    ]);

    // atualiza snapshot de estoque
    $inventory = Inventory::firstOrCreate(
        ['product_id' => $product->id],
        ['qnt_estoque' => 0, 'qnt_ideal' => 0]
    );
    $inventory->increment('qnt_estoque', $data['quantity']);

    return redirect()->route('products.index')
                     ->with('success', 'Produto criado com sucesso!');
}

    /**
     * Remove o produto.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto excluído com sucesso.');
    }
}