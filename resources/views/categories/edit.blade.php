@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-bold text-indigo-700 dark:text-indigo-300 flex items-center gap-3">
        <i class="fa-solid fa-pencil text-indigo-400"></i>
        Editar Categoria
    </h2>
@endsection

@section('content')
<div class="max-w-2xl mx-auto p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 mt-8">
    {{-- Toast de erro --}}
    @if ($errors->any())
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 3000)"
             x-show="show"
             x-transition
             class="flex items-center mb-6 p-4 rounded-lg border border-red-200 bg-red-50 text-red-600 dark:bg-red-900 dark:border-red-700 dark:text-red-200 shadow">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('categories.update', $category) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        @include('categories._form', ['category' => $category])

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('categories.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-400 transition font-semibold shadow-sm">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2 rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 hover:from-indigo-600 hover:to-blue-600 text-white font-bold shadow-lg transition-all flex items-center gap-2">
                <i class="fa-solid fa-check"></i>
                Atualizar Categoria
            </button>
        </div>
    </form>
</div>
@endsection