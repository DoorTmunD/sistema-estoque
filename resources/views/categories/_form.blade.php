@php
    $action = isset($category)
        ? route('categories.update', $category)
        : route('categories.store');
    $method = isset($category) ? 'PUT' : 'POST';
@endphp

<form action="{{ $action }}" method="POST" class="space-y-4">
    @csrf
    @if($method==='PUT') @method('PUT') @endif

    <div>
        <label class="block font-medium">Nome</label>
        <input type="text"
               name="name"
               value="{{ old('name', $category->name ?? '') }}"
               class="w-full border rounded px-3 py-2"
               required>
        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium">Descrição</label>
        <textarea name="description"
                  class="w-full border rounded px-3 py-2">{{ old('description', $category->description ?? '') }}</textarea>
        @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        {{ isset($category) ? 'Atualizar' : 'Cadastrar' }}
    </button>
</form>