{{-- resources/views/products/index.blade.php --}}
@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
        Lista de Produtos
    </h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    <div class="flex justify-end mb-4">
        <a href="{{ route('products.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
            + Novo Produto
        </a>
    </div>

    @if($products->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Nome</th>
                        <th class="px-6 py-3 text-left hidden sm:table-cell">Categoria</th>
                        <th class="px-6 py-3 text-right">Qtd.</th>
                        <th class="px-6 py-3 text-right">Preço Unit.</th>
                        <th class="px-6 py-3 text-right">Preço Total</th>
                        <th class="px-6 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($products as $product)
                        <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            {{-- Nome --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $product->name }}
                            </td>

                            {{-- Categoria --}}
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                {{ $product->category->name ?? '—' }}
                            </td>

                            {{-- Quantidade --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                {{ $product->quantity }}
                            </td>

                            {{-- Preço Unitário --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                R$ {{ number_format($product->unit_price, 2, ',', '.') }}
                            </td>

                            {{-- Preço Total --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                R$ {{ number_format($product->total_price, 2, ',', '.') }}
                            </td>

                            {{-- Ações --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('products.edit', $product) }}"
                                   class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded-lg transition">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                                <form action="{{ route('products.destroy', $product) }}"
                                      method="POST"
                                      class="inline-block ml-2"
                                      onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                                        <i class="fas fa-trash mr-1"></i>Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400">Nenhum produto encontrado.</p>
    @endif
</div>
@endsection