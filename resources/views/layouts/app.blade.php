<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: (localStorage.getItem('dark') === 'true') }"
      x-init="document.documentElement.classList.toggle('dark', dark)">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Flowbite CSS (utilizado em alguns componentes) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css"/>

    @vite(
  ['resources/css/app.css', 'resources/js/app.js'],
  file_exists(public_path('build/manifest.json')) ? 'build' : 'build/.vite'
)
    @livewireStyles

    <!-- Libs pesadas com defer (não bloqueiam a navegação) -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts" defer></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" defer></script>

    @stack('styles')
    <style>
      [x-cloak]{display:none!important}
      html, body { scroll-behavior: smooth; }
      .theme-transition, .theme-transition * { transition: background-color .3s, color .3s; }
      .toast-fade-in { animation: toast-fade-in .45s cubic-bezier(.4,0,.2,1) both; }
      @keyframes toast-fade-in {
        0% { opacity: 0; transform: translateY(-24px) scale(.98); }
        100%{ opacity: 1; transform: translateY(0) scale(1); }
      }
    </style>
</head>
<body class="theme-transition font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">

    @if(config('app.demo'))
      <div class="fixed top-4 right-4 bg-yellow-400 text-black px-3 py-1 rounded-full font-semibold z-50 shadow-2xl">
        DEMO
      </div>
    @endif

    {{-- TOAST UNIFICADO --}}
    @if (session('toast') || session('success') || $errors->any())
      @php
        $toastType = session('success') ? 'success' : ($errors->any() ? 'error' : 'info');
        $toastMsg  = session('success') ?? $errors->first() ?? session('toast');
        $bg = [
          'success' => 'bg-green-600/95',
          'error'   => 'bg-red-600/95',
          'info'    => 'bg-purple-600/90',
        ][$toastType];
      @endphp
      <div x-data="{show:true}" x-show="show" x-transition role="alert"
           class="fixed top-4 right-4 {{ $bg }} text-white px-6 py-3 rounded-xl shadow-2xl toast-fade-in z-50 flex items-center gap-2 min-w-[220px]">
        <span class="sr-only">Notificação</span>
        <span>{{ $toastMsg }}</span>
        <button @click="show=false" class="ml-3 focus:outline-none hover:bg-white/10 px-2 py-1 rounded" aria-label="Fechar notificação">
          <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
    @endif

    <div class="min-h-screen flex flex-col">
        {{-- NAV --}}
        <livewire:layout.navigation />

        {{-- Cabeçalho dinâmico --}}
        @hasSection('header')
            <header class="bg-white/95 dark:bg-gray-800/95 shadow-md backdrop-blur-md transition">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif

        {{-- Conteúdo principal --}}
        <main x-data="{show:false}" x-init="setTimeout(()=>show=true, 60)" x-show="show"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 translate-y-3"
              x-transition:enter-end="opacity-100 translate-y-0"
              class="flex-1 py-6">
            <div class="{{ $fullWidth ?? false ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' }}">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- Livewire scripts (com navegação explícita desabilitada) --}}
    @livewireScripts
    @livewireScriptConfig(['navigate' => false])

    @stack('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js" defer></script>
    <script>
      // Dark mode global unificado (chave: 'dark')
      (function () {
        const saved = localStorage.getItem('dark');
        const isDark = saved === 'true';
        document.documentElement.classList.toggle('dark', isDark);
        // botão opcional: window.toggleTheme()
        window.toggleTheme = () => {
          const now = !document.documentElement.classList.contains('dark');
          document.documentElement.classList.toggle('dark', now);
          localStorage.setItem('dark', now ? 'true' : 'false');
        };
      })();

      // Hardening: se houver links com wire:navigate no menu, removemos para evitar "voltar sozinho"
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('nav a[wire\\:navigate]').forEach(a => a.removeAttribute('wire:navigate'));
      });
    </script>

    {{-- Busca global (renderiza só se habilitado) --}}
    @if(config('estocore.global_search_enabled'))
      @livewire('search-global')
    @endif
</body>
</html>
