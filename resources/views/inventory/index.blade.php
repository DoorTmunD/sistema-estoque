@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Estoque</h2>
@endsection

@section('content')
<div 
    x-data="{
        showExit: false,
        exit: { product_id: null, quantity: 1, responsible_id: '', notes: '' },
        openExit(productId) {
            this.exit = { product_id: productId, quantity: 1, responsible_id: '', notes: '' };
            this.showExit = true;
        },
        closeExit() {
            this.showExit = false;
        }
    }"
    class="bg-white shadow rounded-lg p-6"
>
    <div class="flex justify-between items-center mb-4">
        <div class="flex justify-end mb-4">
        <a href="{{ route('inventory.export') }}"
           class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
            Exportar CSV
        </a>
    </div>
        <h3 class="text-lg font-medium text-gray-700 dark:text-gray-300">Lista de Estoque</h3>
        <a
            href="{{ route('inventory.create') }}"
            class="px-4 py-2 bg-green-600 text-white font-semibold rounded-lg shadow hover:bg-green-700 active:bg-green-800 transition"
        >
            + Novo Registro de Estoque
        </a>
    </div>

    {{-- filtro mantido aqui... --}}

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Produto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categoria</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Fornecedor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qtd Estoque</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qtd Ideal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200">
                @foreach($inventories as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 odd:bg-white even:bg-gray-50 dark:even:bg-gray-700">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $item->product->category->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $item->product->supplier->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $item->qnt_estoque }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-200">{{ $item->qnt_ideal }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            {{-- Editar estoque existente --}}
                            <a
                                href="{{ route('inventory.edit', $item) }}"
                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-200"
                            >
                                Editar
                            </a>
                            {{-- Excluir --}}
                            <form action="{{ route('inventory.destroy', $item) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-200"
                                    onclick="return confirm('Tem certeza que deseja excluir este registro de estoque?')"
                                >
                                    Excluir
                                </button>
                            </form>
                            {{-- Dar Baixa (saída) --}}
                            <button
                                type="button"
                                @click="openExit({{ $item->product->id }})"
                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-200"
                            >
                                Dar Baixa
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $inventories->links() }}
    </div>

    {{-- Modal de Saída --}}
    <div
        x-show="showExit"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50"
    >
        <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden w-full max-w-md">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Registrar Saída</h3>
            </div>
            <form action="{{ route('movements.exit') }}" method="POST" class="p-4 space-y-4">
                @csrf
                <input type="hidden" name="product_id" :value="exit.product_id" />

                {{-- Quantidade --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade</label>
                    <input
                        type="number"
                        name="quantity"
                        x-model.number="exit.quantity"
                        min="1"
                        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    />
                    @error('quantity') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Responsável --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Responsável</label>
                    <select
                        name="responsible_id"
                        x-model="exit.responsible_id"
                        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    >
                        <option value="">-- selecione --</option>
                        @foreach(\App\Models\User::orderBy('name')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('responsible_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                </div>

                {{-- Observações --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
                    <textarea
                        name="notes"
                        x-model="exit.notes"
                        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
                    ></textarea>
                </div>

                {{-- Ações --}}
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="closeExit()"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 transition"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
                    >
                        Confirmar Saída
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection