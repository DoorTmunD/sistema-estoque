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

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

@vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
      [x-cloak]{display:none!important}
      @keyframes shake {
        10%, 90% { transform: translateX(-2px); }
        20%, 80% { transform: translateX(4px); }
        30%, 50%, 70% { transform: translateX(-8px); }
        40%, 60% { transform: translateX(8px); }
      }
      .animate-shake { animation: shake .5s; }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex flex-row bg-gray-100 dark:bg-[#181A20] relative overflow-hidden">

    <!-- Coluna Esquerda -->
    <div class="relative w-1/2 hidden lg:flex flex-col justify-center items-center bg-gradient-to-br from-[#e4ecfa] via-[#c7d6ee] to-[#e4ecfa] dark:from-[#202544] dark:via-[#1a1d2e] dark:to-[#202544] transition-all duration-500">
        <div class="absolute inset-0 z-0 pointer-events-none opacity-70">
            @include('layouts.svg-background')
        </div>
        <div class="relative z-10 w-full h-full flex items-center justify-center pointer-events-none">
            <lottie-player
                src="https://assets6.lottiefiles.com/packages/lf20_9wpyhdzo.json"
                background="transparent" speed="1" loop autoplay
                style="width: 80%; max-width: 600px; height: 500px;">
            </lottie-player>
        </div>
        <div class="absolute bottom-10 left-0 w-full text-center">
            <span class="text-lg text-gray-500 dark:text-gray-300 font-medium tracking-wide">
                Sistema EstoCORE — Centralize. Controle. Evolua.
            </span>
        </div>
    </div>

    <!-- Coluna Direita (Card) -->
    <div class="w-full lg:w-1/2 flex items-center justify-center min-h-screen relative bg-white/80 dark:bg-[#232046]/60 transition-all duration-500 z-20">
<div x-data="{ error: false }"
      :class="error ? 'animate-shake ring-2 ring-red-400 shadow-red-200' : ''"
      class="relative w-full max-w-md mx-auto bg-white/95 dark:bg-gray-900/90 rounded-2xl shadow-2xl backdrop-blur-xl border border-indigo-100/60 dark:border-[#6648e033] px-10 py-10 flex flex-col items-center space-y-6">
    {{ $slot }}
</div>
    </div>
</div>

<!-- Lottie -->
<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" defer></script>
</body>
</html>
