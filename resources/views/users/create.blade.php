@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">Novo Usuário</h2>
@endsection

@section('content')
@if(session('success'))
    <div 
     x-data="{ show: true }" 
     x-init="setTimeout(() => show = false, 3500)" 
     x-show="show" 
     x-transition 
     class="flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800 fixed top-5 right-6 z-50"
     role="alert"
     id="toast-success">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
            <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_xlkxtmul.json"
                   background="transparent"  speed="1"  style="width: 40px; height: 40px;"  autoplay>
            </lottie-player>
        </div>
        <div class="ml-3 text-sm font-normal">{{ session('success') }}</div>
        <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 dark:bg-gray-800 dark:text-gray-500 dark:hover:text-white"
                data-dismiss-target="#toast-success" aria-label="Close">
            <span class="sr-only">Fechar</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
        </button>
    </div>
@endif

@if ($errors->any())
    <div id="toast-danger" class="flex items-center w-full max-w-xs p-4 mb-4 text-red-500 bg-white rounded-lg shadow dark:text-red-400 dark:bg-gray-800 fixed top-5 right-6 z-50"
         role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <div class="ml-3 text-sm font-normal">{{ $errors->first() }}</div>
        <button type="button"
                class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 inline-flex h-8 w-8 dark:bg-gray-800 dark:text-red-500 dark:hover:text-white"
                data-dismiss-target="#toast-danger" aria-label="Close">
            <span class="sr-only">Fechar</span>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"/>
            </svg>
        </button>
    </div>
@endif

<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
    @include('users._form')
</div>
@endsection