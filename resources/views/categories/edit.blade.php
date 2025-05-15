@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">
        Editar Categoria
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow">
    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        @include('categories._form', ['category' => $category])

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('categories.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">
                Atualizar Categoria
            </button>
        </div>
    </form>
</div>
@endsection