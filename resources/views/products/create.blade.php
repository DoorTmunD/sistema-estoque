@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Novo Produto') }}
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Cadastrar Novo Produto</h3>
    </div>
    <div class="p-6">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nome --}}
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
                <input id="name" name="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 
                              dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"/>
                @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
                <textarea id="description" name="description"
                          class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 
                                 dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"
                          rows="3"
                >{{ old('description') }}</textarea>
            </div>

            {{-- Categoria & Fornecedor --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
                    <select id="category_id" name="category_id" required
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 
                                   dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- selecione --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id')==$cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fornecedor</label>
                    <select id="supplier_id" name="supplier_id" required
                            class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 
                                   dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- selecione --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" @selected(old('supplier_id')==$sup->id)>
                                {{ $sup->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Quantidade Inicial & Preço Unitário --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade Inicial</label>
                    <input id="quantity" name="quantity" type="number" min="1"
                           value="{{ old('quantity', 1) }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 
                                  dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"/>
                    @error('quantity') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preço Unitário</label>
                    <input id="unit_price" name="unit_price" type="text"
                           value="{{ old('unit_price', '0,00') }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 
                                  dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"/>
                    @error('unit_price') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Botão --}}
            <div class="pt-4 text-right">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow transition">
                    Criar Produto e Entrada
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Cleave('#unit_price', {
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
            delimiter: '.',
            numeralDecimalMark: ',',
            numeralDecimalScale: 2
        });
    });
</script>
@endpush