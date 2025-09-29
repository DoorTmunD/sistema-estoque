@php
  $action = isset($product) ? route('products.update', $product) : route('products.store');
  $method = isset($product) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @if($method === 'PUT') @method('PUT') @endif

  {{-- Nome --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome *</label>
    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}"
           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
    @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Preço de Custo (custo médio inicial) --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preço de Custo *</label>
    <input type="text" name="price_custo" value="{{ old('price_custo', isset($product)? number_format($product->avg_cost ?? 0,2,',','.') : '') }}"
           placeholder="Ex.: 250,00"
           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
    @error('price_custo')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Categoria --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categoria *</label>
    <select name="category_id"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
      <option value="">— Selecione —</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
      @endforeach
    </select>
    @error('category_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Fornecedor --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fornecedor *</label>
    <select name="supplier_id"
            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" required>
      <option value="">— Selecione —</option>
      @foreach($suppliers as $sup)
        <option value="{{ $sup->id }}" @selected(old('supplier_id', $product->supplier_id ?? '') == $sup->id)>{{ $sup->name }}</option>
      @endforeach
    </select>
    @error('supplier_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  {{-- Quantidade inicial (gera itens) --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Quantidade inicial</label>
    <input type="number" name="initial_qty" min="0" value="{{ old('initial_qty', '') }}"
           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    @error('initial_qty')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    <p class="text-xs text-gray-500 mt-1">Para itens não consumíveis, cria N itens <em>AVAILABLE</em> com serial interno automático.</p>
  </div>

  {{-- Estoque mínimo (alerta) --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Estoque mínimo</label>
    <input type="number" name="min_stock" min="0" value="{{ old('min_stock', $product->min_stock ?? 0) }}"
           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
  </div>

  {{-- Imagem --}}
  <div>
    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto do Produto (opcional)</label>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.tiff"
           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
    @error('image')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
  </div>

  <div class="flex justify-end">
    <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
      {{ isset($product) ? 'Salvar alterações' : 'Cadastrar' }}
    </button>
  </div>
</form>
