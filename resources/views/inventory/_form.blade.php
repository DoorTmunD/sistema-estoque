@php
    $action = isset($inventory)
        ? route('inventory.update', $inventory)
        : route('inventory.store');
    $method = isset($inventory) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @method($method)

    <!-- Produto -->
    <div>
        <label class="block font-medium">Produto</label>
        <select
            name="product_id"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
            <option value="">Selecione um produto</option>
            @foreach($products as $product)
                <option
                    value="{{ $product->id }}"
                    @selected(old('product_id', $inventory->product_id ?? '') == $product->id)
                >
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
        @error('product_id')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Quantidade em Estoque -->
    <div>
        <label class="block font-medium">Quantidade em Estoque</label>
        <input
            type="number"
            name="qnt_estoque"
            value="{{ old('qnt_estoque', $inventory->qnt_estoque ?? '') }}"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
        @error('qnt_estoque')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Quantidade Ideal -->
    <div>
        <label class="block font-medium">Quantidade Ideal</label>
        <input
            type="number"
            name="qnt_ideal"
            value="{{ old('qnt_ideal', $inventory->qnt_ideal ?? '') }}"
            class="block w-full rounded-md border-gray-300 focus:ring focus:ring-opacity-50"
        >
        @error('qnt_ideal')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <!-- Botão -->
    <div class="mt-6">
        <button
            type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
            {{ isset($inventory) ? 'Atualizar' : 'Cadastrar' }}
        </button>
    </div>
</form>