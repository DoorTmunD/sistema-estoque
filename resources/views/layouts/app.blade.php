<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: localStorage.getItem('dark') === 'true' }"
      x-init="if (dark) document.documentElement.classList.add('dark')">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

<!-- Manual CSS & JS assets (fallback relative paths) -->
<link rel="stylesheet" href="{{ asset('build/assets/app.css') }}">
<script src="{{ asset('build/assets/app.js') }}" defer></script>

    <!-- Page Specific Styles -->
    @stack('styles')

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200">
    @if(config('app.demo'))
      <div class="fixed top-4 right-4 bg-yellow-400 text-black px-3 py-1 rounded-full font-semibold z-50">
        DEMO
      </div>
    @endif

    <div class="min-h-screen">
        {{-- Navegação --}}
        <livewire:layout.navigation />

        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg animate-fade-in-out z-50">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg animate-fade-in-out z-50">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Cabeçalho dinâmico --}}
        @hasSection('header')
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        {{-- Conteúdo da página --}}
        <main 
            x-data="{ show: false }" 
            x-init="show = true" 
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="py-6"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>