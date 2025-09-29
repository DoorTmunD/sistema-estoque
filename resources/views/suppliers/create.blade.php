@extends('layouts.app')

@section('header')
    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-2">
        <i class="fa-solid fa-truck-field text-green-400"></i>
        {{ isset($supplier) ? 'Editar Fornecedor' : 'Novo Fornecedor' }}
    </h2>
@endsection

@section('content')
    {{-- Sucesso Toast --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3500)" x-show="show" x-transition
            class="flex items-center w-full max-w-xs p-4 mb-6 text-green-700 bg-green-100 rounded-lg shadow fixed top-5 right-6 z-50 dark:bg-green-900 dark:text-green-200"
            role="alert">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M9 12l2 2 4-4"/>
            </svg>
            <div class="flex-1">{{ session('success') }}</div>
            <button @click="show = false" type="button"
                class="ml-4 text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                aria-label="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Erro Toast --}}
    @if ($errors->any())
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
            class="flex items-center w-full max-w-xs p-4 mb-6 text-red-700 bg-red-100 rounded-lg shadow fixed top-5 right-6 z-50 dark:bg-red-900 dark:text-red-200"
            role="alert">
            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <div class="flex-1">{{ $errors->first() }}</div>
            <button @click="show = false" type="button"
                class="ml-4 text-gray-400 hover:text-gray-800 dark:hover:text-white transition"
                aria-label="Fechar">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="max-w-3xl mx-auto p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800">
        {{-- O mesmo _form para create/edit --}}
        @include('suppliers._form', ['supplier' => $supplier ?? null])

        <div class="mt-8 flex flex-col md:flex-row justify-end gap-3">
            <a href="{{ route('suppliers.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2 bg-gray-200 hover:bg-gray-400 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl shadow font-semibold transition-all">
                <i class="fa-solid fa-arrow-left"></i> Cancelar
            </a>
            <button type="submit" form="supplier-form"
                class="inline-flex items-center gap-2 px-6 py-2 bg-gradient-to-br from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-500 text-white rounded-xl font-bold shadow-lg transition-all text-base">
                <i class="fa-solid fa-check"></i>
                {{ isset($supplier) ? 'Atualizar Fornecedor' : 'Salvar Fornecedor' }}
            </button>
        </div>
    </div>
@endsection