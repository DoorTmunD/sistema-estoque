@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Histórico de Movimentações</h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
    {{-- Filtro por produto --}}
    <form method="GET" class="mb-6 flex items-center gap-4">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Produto</label>
            <select
                name="product_id"
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
            >
                <option value="">Todos Produtos</option>
                @foreach(\App\Models\Product::orderBy('name')->get() as $product)
                    <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>
                        {{ $product->name }} ({{ $product->supplier->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <button
            type="submit"
            class="mt-6 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
        >
            Filtrar
        </button>
        @if(request()->filled('product_id'))
            <a
                href="{{ route('movements.index') }}"
                class="mt-6 px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition"
            >
                Limpar
            </a>
        @endif
    </form>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Data
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Produto
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Tipo
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Quantidade
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Preço Unitário
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Total
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Responsável
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Observações
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($movements as $move)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $move->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $move->product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium 
                                   {{ $move->type === 'in' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $move->type === 'in' ? 'Entrada' : 'Saída' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">
                            {{ $move->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">
                            R$ {{ number_format($move->unit_price, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200 text-right">
                            R$ {{ number_format($move->total_price, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $move->responsible?->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">
                            {{ $move->notes }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $movements->links() }}
    </div>
</div>
@endsection