@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
        Registrar Entrada de Produto
    </h2>
@endsection

@section('content')
<div 
  class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
  x-data="{
    product_id: '{{ old('product_id') }}',
    quantity: {{ old('quantity', 1) }},
    unitPrice: {{ old('unit_price', 0) }},
    get total() { return (this.quantity * this.unitPrice).toFixed(2) }
  }"
>
  <form action="{{ route('movements.entry') }}" method="POST" class="space-y-4">
    @csrf

    {{-- Produto --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Produto</label>
      <select
        name="product_id"
        x-model="product_id"
        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
      >
        <option value="">-- selecione --</option>
        @foreach(\App\Models\Product::orderBy('name')->get() as $product)
          <option value="{{ $product->id }}" @selected(old('product_id')==$product->id)>
            {{ $product->name }} ({{ $product->supplier->name }})
          </option>
        @endforeach
      </select>
      @error('product_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Quantidade --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade</label>
      <input
        type="number" name="quantity" min="1"
        x-model.number="quantity"
        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
      />
      @error('quantity') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Preço Unitário --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Preço Unitário</label>
      <input
        type="number" name="unit_price" step="0.01" min="0"
        x-model.number="unitPrice"
        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
      />
      @error('unit_price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    {{-- Total (readonly) --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
      <input
        type="text" readonly
        :value="total"
        class="mt-1 block w-full rounded border-gray-300 bg-gray-100 dark:bg-gray-700 dark:text-gray-200"
      />
    </div>

    {{-- Observações --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
      <textarea
        name="notes"
        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
      >{{ old('notes') }}</textarea>
    </div>

    {{-- Botão --}}
    <div>
      <button
        type="submit"
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
      >
        Registrar Entrada
      </button>
    </div>
  </form>
</div>
@endsection