@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
        <svg class="w-8 h-8 text-indigo-500 drop-shadow-[0_0_8px_#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Categoria
    </h2>
@endsection

@section('content')
    {{-- Toast de sucesso --}}
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3500)"
            x-show="show"
            x-transition
            class="fixed top-6 right-6 z-50 flex items-center max-w-xs px-5 py-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-green-200 dark:border-green-700 animate__animated animate__fadeInDown"
            role="alert">
            <div class="inline-flex items-center justify-center w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full">
                <svg class="w-7 h-7 text-green-500 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="ml-3 text-green-700 dark:text-green-200 text-base font-semibold">{{ session('success') }}</span>
            <button @click="show=false" class="ml-auto text-gray-400 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                <span class="sr-only">Fechar</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    {{-- Toast de erro --}}
    @if ($errors->any())
        <div 
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 4000)"
            x-show="show"
            x-transition
            class="fixed top-6 right-6 z-50 flex items-center max-w-xs px-5 py-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-red-200 dark:border-red-700 animate__animated animate__fadeInDown"
            role="alert">
            <div class="inline-flex items-center justify-center w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full">
                <svg class="w-7 h-7 text-red-500 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <span class="ml-3 text-red-700 dark:text-red-200 text-base font-semibold">{{ $errors->first() }}</span>
            <button @click="show=false" class="ml-auto text-gray-400 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                <span class="sr-only">Fechar</span>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <div class="max-w-2xl mx-auto p-8 bg-white dark:bg-gray-900 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-800 mt-8">
        {{-- Se quiser trocar entre criar/editar, só passar $category --}}
        @include('categories._form', ['category' => $category ?? null])
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('categories.index') }}"
               class="px-4 py-2 rounded-lg border border-gray-300 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-400 transition font-semibold shadow-sm">
                Cancelar
            </a>
            {{-- Botão "Salvar Categoria" só aparece se NÃO for edição --}}
            @if (!isset($category))
                <button type="submit" form="category-form"
                        class="px-6 py-2 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold shadow-lg transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check"></i>
                    Salvar Categoria
                </button>
            @endif
        </div>
    </div>
@endsection