@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Lista de Produtos</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-md">
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="flex items-center w-full max-w-xs p-4 mb-4 bg-white rounded-lg shadow fixed top-5 right-6 z-50">
            <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_xlkxtmul.json" background="transparent" speed="1" style="width: 40px; height: 40px;" autoplay></lottie-player>
            <div class="ml-3 text-sm font-normal">{{ session('success') }}</div>
        </div>
    @endif

    <div class="flex justify-end mb-4">
        <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
            + Novo Produto
        </a>
    </div>

    @if($products->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-auto">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <th class="px-6 py-3 text-left">Foto</th>
                        <th class="px-6 py-3 text-left">Nome</th>
                        <th class="px-6 py-3 text-left hidden sm:table-cell">Categoria</th>
                        <th class="px-6 py-3 text-right">Disponíveis</th>
                        <th class="px-6 py-3 text-right">Preço Unit.</th>
                        <th class="px-6 py-3 text-right">Preço Total</th>
                        <th class="px-6 py-3 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($products as $product)
                        @php
                            $available = (int) $product->available_count;
                            $unit      = (float) ($product->avg_cost ?? $product->unit_price ?? 0);
                            $total     = (float) $product->total_price;
                            $fmt       = fn($v) => 'R$ ' . number_format((float)$v, 2, ',', '.');
                        @endphp
                        <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div style="width:48px;height:48px;position:relative;">
                                    @if(!empty($product->image_path))
                                        <img src="{{ asset('storage/'.$product->image_path) }}" class="w-12 h-12 object-cover rounded shadow border border-gray-300" alt="Foto do produto" style="width:48px;height:48px;object-fit:cover;" onerror="this.style.display='none';this.nextElementSibling.style.display='block';"/>
                                        <div style="display:none;position:absolute;top:0;left:0;width:48px;height:48px;background:transparent;">
                                            <lottie-player src="https://assets1.lottiefiles.com/private_files/lf30_kqshlcsb.json" background="transparent" speed="1" style="width:48px;height:48px;" loop autoplay></lottie-player>
                                        </div>
                                    @else
                                        <div style="width:48px;height:48px;">
                                            <lottie-player src="https://assets1.lottiefiles.com/private_files/lf30_kqshlcsb.json" background="transparent" speed="1" style="width:48px;height:48px;" loop autoplay></lottie-player>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $product->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">{{ $available }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">{{ $fmt($unit) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">{{ $fmt($total) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <a href="{{ route('products.edit', $product) }}" class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-sm rounded-lg transition">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline-block ml-2" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg transition">
                                        <i class="fas fa-trash mr-1"></i>Excluir
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between">
            {{ $products->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-16">
            <lottie-player src="https://assets1.lottiefiles.com/packages/lf20_jyye9mjx.json" background="transparent" speed="1" style="width: 100px; height: 100px;" loop autoplay></lottie-player>
            <p class="mt-4 text-gray-400 text-lg">Nenhum produto encontrado.</p>
        </div>
    @endif
</div>
@endsection
