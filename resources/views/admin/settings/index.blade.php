@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-8 flex items-center gap-3 select-none">
        <svg class="w-7 h-7 text-yellow-500 animate-spin-slow drop-shadow-[0_0_8px_#fde047]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M12 4v2m0 12v2m8-8h2M4 12H2m15.364-7.364l1.414 1.414M6.222 17.778l-1.414 1.414M17.778 17.778l1.414-1.414M6.222 6.222L4.808 4.808" />
        </svg>
        Configurações do Sistema
    </h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!-- Notificações -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-blue-400 group relative">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-7 h-7 text-blue-500 group-hover:animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M15 17h5l-1.405-1.405M4 4v16a1 1 0 001 1h3m10-4l1.405-1.405M15 17a2 2 0 01-2 2h-2a2 2 0 01-2-2v-5a2 2 0 012-2h2a2 2 0 012 2v5z" />
                </svg>
                <span class="font-semibold text-lg">Notificações</span>
            </div>
            <p class="text-gray-500 dark:text-gray-300 mb-4">Personalize as notificações por email e in-app.</p>
            <a href="{{ route('admin.settings.notifications') }}"
               class="mt-auto px-4 py-2 rounded bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition shadow hover:scale-105 font-bold flex items-center gap-1">
                <i class="fa-solid fa-sliders"></i>
                Configurar
            </a>
        </div>

        <!-- Backup -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-yellow-400 group relative">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7">
                    <lottie-player src="https://assets9.lottiefiles.com/packages/lf20_5g5kzqbb.json" background="transparent" speed="1" loop autoplay style="width:28px; height:28px;"></lottie-player>
                </div>
                <span class="font-semibold text-lg">Backup</span>
                <span class="ml-2 text-xs bg-yellow-200 text-yellow-900 rounded-full px-2 py-0.5 font-bold animate-pulse">Beta</span>
            </div>
            <p class="text-gray-500 dark:text-gray-300 mb-4">Realize backup dos dados do sistema. <span class="text-xs bg-gray-200 dark:bg-gray-900 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded ml-1">Último: {{ now()->subDays(1)->format('d/m/Y H:i') }}</span></p>
            <a href="{{ route('admin.settings.backup') }}"
               class="mt-auto px-4 py-2 rounded bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 hover:bg-yellow-200 dark:hover:bg-yellow-800 transition shadow hover:scale-105 font-bold flex items-center gap-1">
                <i class="fa-solid fa-cloud-arrow-up"></i>
                Ver opções
            </a>
        </div>

        <!-- Permissões -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-green-400 group relative">
            <div class="flex items-center gap-2 mb-3">
                <svg class="w-7 h-7 text-green-500 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M12 8c-1.104 0-2 .896-2 2 0 1.657 2 4 2 4s2-2.343 2-4c0-1.104-.896-2-2-2z" />
                </svg>
                <span class="font-semibold text-lg">Permissões</span>
            </div>
            <p class="text-gray-500 dark:text-gray-300 mb-4">Defina permissões e cargos para usuários.</p>
            <a href="{{ route('admin.settings.permissions') }}"
               class="mt-auto px-4 py-2 rounded bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800 transition shadow hover:scale-105 font-bold flex items-center gap-1">
                <i class="fa-solid fa-key"></i>
                Gerenciar
            </a>
        </div>

        <!-- Integrações/API -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-indigo-400 group relative">
            <div class="flex items-center gap-2 mb-3">
                <i class="fa-solid fa-plug text-indigo-500 text-2xl group-hover:animate-bounce"></i>
                <span class="font-semibold text-lg">Integrações/API</span>
                <span class="ml-2 text-xs bg-indigo-100 text-indigo-800 rounded-full px-2 py-0.5 font-bold">Novo</span>
            </div>
            <p class="text-gray-500 dark:text-gray-300 mb-4">Conecte o sistema a ERPs, marketplaces ou gere tokens de API.</p>
            <a href="#" class="mt-auto px-4 py-2 rounded bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 hover:bg-indigo-200 dark:hover:bg-indigo-800 transition shadow hover:scale-105 font-bold flex items-center gap-1 opacity-60 cursor-not-allowed">
                <i class="fa-solid fa-link"></i>
                Em breve
            </a>
        </div>

        <!-- Status do Sistema -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-cyan-400 group relative">
            <div class="flex items-center gap-2 mb-3">
                <i class="fa-solid fa-signal text-cyan-500 text-2xl group-hover:animate-bounce"></i>
                <span class="font-semibold text-lg">Status do Sistema</span>
            </div>
            <div class="flex items-center gap-3 mb-2">
                <span class="inline-flex items-center text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded font-bold">
                    <i class="fa-solid fa-circle text-green-400 text-xxs mr-1 animate-pulse"></i>
                    Online
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-300">
                    Versão {{ config('app.version', '0.8.8') }}
                </span>
            </div>
            <p class="text-gray-500 dark:text-gray-300 mb-4">Tudo operando normalmente. Último deploy: {{ now()->subHours(7)->format('d/m/Y H:i') }}</p>
            <span class="text-xs text-gray-400 dark:text-gray-500">Uptime: 99.99%</span>
        </div>
        
<!-- Configuração de E-mail -->
<div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-cyan-400 group relative">
    <div class="flex items-center gap-2 mb-3">
        <i class="fa-solid fa-envelope-circle-check text-cyan-400 text-2xl group-hover:animate-bounce"></i>
        <span class="font-semibold text-lg">E-mail</span>
        <span class="ml-2 text-xs bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200 rounded-full px-2 py-0.5 font-bold select-none">SMTP</span>
    </div>
    <p class="text-gray-500 dark:text-gray-300 mb-4">
        Configuração de envio de e-mails do sistema (SMTP, notificações, remetente, etc).
    </p>
    <div class="flex flex-row gap-2 mt-auto">
        <a href="{{ route('admin.settings.email') }}"
           class="px-4 py-2 rounded bg-cyan-100 dark:bg-cyan-900 text-cyan-700 dark:text-cyan-200 hover:bg-cyan-200 dark:hover:bg-cyan-800 transition shadow hover:scale-105 font-bold flex items-center gap-2">
            <i class="fa-solid fa-gear"></i> Configurar
        </a>
        @if(auth()->user() && in_array(auth()->user()->nivel, ['super-admin', 'adm']))
            <a href="{{ route('admin.logs.email') }}"
               class="px-4 py-2 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-800 transition shadow hover:scale-105 font-bold flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list"></i> Log
            </a>
        @endif
    </div>
    @if(auth()->user() && in_array(auth()->user()->nivel, ['super-admin', 'adm']))
        <span class="absolute top-2 right-2 text-xs bg-cyan-50 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-200 rounded px-2 py-0.5 select-none border border-cyan-200 dark:border-cyan-800 font-bold tracking-tight">
            Visualização e relatórios
        </span>
    @endif
</div>

        <!-- Ajuda/Documentação -->
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-xl flex flex-col items-start transition-all duration-300 hover:scale-105 hover:shadow-2xl border-l-4 border-pink-400 group relative">
            <div class="flex items-center gap-2 mb-3">
                <i class="fa-solid fa-book-open text-pink-400 text-2xl group-hover:animate-pulse"></i>
                <span class="font-semibold text-lg">Ajuda &amp; Documentação</span>
            </div>
            <p class="text-gray-500 dark:text-gray-300 mb-4">Acesse tutoriais, FAQs e vídeo de onboarding.</p>
            <a href="https://github.com/DoorTmunD/sistema-estoque/wiki" target="_blank"
               class="mt-auto px-4 py-2 rounded bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-200 hover:bg-pink-200 dark:hover:bg-pink-800 transition shadow hover:scale-105 font-bold flex items-center gap-1">
                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                Ver ajuda
            </a>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    @endpush
@endsection