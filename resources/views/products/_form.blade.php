@php
  $action = isset($product)
    ? route('products.update', $product)
    : route('products.store');
  $method = isset($product) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4">
  @csrf
  @method($method)

  {{-- Nome --}}
  <div>
    <label class="block font-medium">Nome</label>
    <input
      type="text"
      name="name"
      value="{{ old('name', $product->name ?? '') }}"
      class="w-full border rounded px-3 py-2"
    />
    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Preço de Custo --}}
  <div>
    <label class="block font-medium">Preço de Custo</label>
    <input
      type="text"
      id="price_custo"
      name="price_custo"
      value="{{ old('price_custo', $product->price_custo ?? '') }}"
      class="w-full border rounded px-3 py-2"
    />
    @error('price_custo')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Preço de Venda --}}
  <div>
    <label class="block font-medium">Preço de Venda</label>
    <input
      type="text"
      id="price_venda"
      name="price_venda"
      value="{{ old('price_venda', $product->price_venda ?? '') }}"
      class="w-full border rounded px-3 py-2"
    />
    @error('price_venda')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Categoria --}}
  <div>
    <label class="block font-medium">Categoria</label>
    <select name="category_id" class="w-full border rounded px-3 py-2">
      <option value="">— Selecione —</option>
      @foreach($categories as $cat)
        <option
          value="{{ $cat->id }}"
          {{ old('category_id', $product->category_id ?? '') == $cat->id ? 'selected' : '' }}
        >{{ $cat->name }}</option>
      @endforeach
    </select>
    @error('category_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Fornecedor --}}
  <div>
    <label class="block font-medium">Fornecedor</label>
    <select name="supplier_id" class="w-full border rounded px-3 py-2">
      <option value="">— Selecione —</option>
      @foreach($suppliers as $sup)
        <option
          value="{{ $sup->id }}"
          {{ old('supplier_id', $product->supplier_id ?? '') == $sup->id ? 'selected' : '' }}
        >{{ $sup->name }}</option>
      @endforeach
    </select>
    @error('supplier_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Botão --}}
  <div class="mt-6">
    <button
      type="submit"
      class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
    >
      {{ isset($product) ? 'Atualizar' : 'Cadastrar' }}
    </button>
  </div>
</form>