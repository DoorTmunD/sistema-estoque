@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('Editar Produto') }}
    </h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
    <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        {{-- Nome --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
            <input
                name="name"
                value="{{ old('name', $product->name) }}"
                required
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 
                       dark:bg-gray-700 dark:text-gray-200"
            />
            @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Descrição --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Descrição</label>
            <textarea
                name="description"
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 
                       dark:bg-gray-700 dark:text-gray-200"
            >{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- Categoria --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria</label>
            <select
                name="category_id"
                required
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 
                       dark:bg-gray-700 dark:text-gray-200"
            >
                <option value="">-- selecione --</option>
                @foreach($categories as $cat)
                    <option
                        value="{{ $cat->id }}"
                        @selected(old('category_id', $product->category_id) == $cat->id)
                    >
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Fornecedor --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Fornecedor</label>
            <select
                name="supplier_id"
                required
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 
                       dark:bg-gray-700 dark:text-gray-200"
            >
                <option value="">-- selecione --</option>
                @foreach($suppliers as $sup)
                    <option
                        value="{{ $sup->id }}"
                        @selected(old('supplier_id', $product->supplier_id) == $sup->id)
                    >
                        {{ $sup->name }}
                    </option>
                @endforeach
            </select>
            @error('supplier_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Quantidade  --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade </label>
            <input
                name="quantity"
                type="number"
                min="1"
                value="{{ old('quantity', $product->quantity) }}"
                required
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 
                       dark:bg-gray-700 dark:text-gray-200"
            />
            @error('quantity') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Preço Unitário --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preço Unitário</label>
            <input
                name="unit_price"
                type="number"
                step="0.01"
                min="0"
                value="{{ old('unit_price', $product->unit_price) }}"
                required
                class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 
                       dark:bg-gray-700 dark:text-gray-200"
            />
            @error('unit_price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        {{-- Botão de Atualizar --}}
        <div class="pt-4">
            <button
                type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
            >
                Atualizar Produto
            </button>
        </div>
    </form>
</div>
@endsection