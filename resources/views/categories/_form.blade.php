@php
    $action = isset($category)
        ? route('categories.update', $category)
        : route('categories.store');
    $method = isset($category) ? 'PUT' : 'POST';
@endphp

<form id="category-form" action="{{ $action }}" method="POST" class="space-y-8">
    @csrf
    @if($method==='PUT') @method('PUT') @endif

    {{-- Nome --}}
    <div>
        <label for="name" class="block mb-2 text-base font-semibold text-gray-900 dark:text-gray-100">Nome <span class="text-pink-600">*</span></label>
        <div class="relative group">
            <span class="absolute left-3 top-2.5 text-gray-400 group-focus-within:text-indigo-500 pointer-events-none">
                <i class="fa-solid fa-tags"></i>
            </span>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', $category->name ?? '') }}"
                class="pl-10 pr-4 py-2 w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white shadow focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                placeholder="Digite o nome da categoria"
                required
                autofocus
            >
        </div>
        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Descrição --}}
    <div>
        <label for="description" class="block mb-2 text-base font-semibold text-gray-900 dark:text-gray-100">Descrição</label>
        <textarea name="description"
            id="description"
            class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white shadow focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition p-3"
            rows="3"
            placeholder="Adicione uma descrição (opcional)"
        >{{ old('description', $category->description ?? '') }}</textarea>
        @error('description') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    {{-- Botão de editar (se estiver editando) --}}
    @if(isset($category))
        <button type="submit"
            class="flex items-center gap-2 px-6 py-2 bg-gradient-to-br from-blue-600 to-indigo-700 hover:from-indigo-600 hover:to-blue-700 text-white rounded-lg font-bold shadow-lg transition-all mt-4">
            <i class="fa-solid fa-rotate"></i>
            Atualizar Categoria
        </button>
    @endif
</form>