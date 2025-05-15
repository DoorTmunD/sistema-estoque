@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">
        Novo Fornecedor
    </h2>
@endsection

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded-lg shadow">
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf

        @include('suppliers._form') {{-- aqui devem estar os campos name, email, phone, address, etc --}}

        <div class="mt-6 flex justify-end space-x-2">
            <a href="{{ route('suppliers.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                Cancelar
            </a>
            <button type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                Salvar Fornecedor
            </button>
        </div>
    </form>
</div>
@endsection