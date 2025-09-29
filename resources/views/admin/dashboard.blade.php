@extends('layouts.admin')

@section('content')
    <h1 class="text-3xl font-bold mb-8 flex items-center gap-2">
        <svg class="w-8 h-8 text-indigo-500 drop-shadow-[0_0_8px_#6366F1]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Painel Administrativo
    </h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        {{-- Usuários --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col justify-between transition group border-l-4 border-blue-400 hover:scale-105 hover:shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-users-gear text-blue-500 text-3xl group-hover:animate-pulse"></i>
                <span class="text-lg font-bold text-gray-800 dark:text-white">Usuários</span>
            </div>
            <div class="text-2xl font-extrabold mb-2">{{ \App\Models\User::count() }}</div>
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">Cadastrados</div>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-blue-600 hover:underline font-bold flex items-center gap-1 group-hover:text-blue-700">
                    Gerenciar
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Logs de Auditoria --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col justify-between transition group border-l-4 border-green-400 hover:scale-105 hover:shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-shield-halved text-green-500 text-3xl group-hover:animate-pulse"></i>
                <span class="text-lg font-bold text-gray-800 dark:text-white">Logs de Auditoria</span>
            </div>
            <div class="text-2xl font-extrabold mb-2">{{ \Spatie\Activitylog\Models\Activity::count() }}</div>
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">Eventos logados</div>
                <a href="{{ route('admin.logs.index') }}" class="text-xs text-green-600 hover:underline font-bold flex items-center gap-1 group-hover:text-green-700">
                    Consultar
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Configurações --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col justify-between transition group border-l-4 border-yellow-400 hover:scale-105 hover:shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-gear text-yellow-500 text-3xl group-hover:animate-spin"></i>
                <span class="text-lg font-bold text-gray-800 dark:text-white">Configurações</span>
            </div>
            <div class="text-2xl font-extrabold mb-2">Pronto</div>
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">Sistema</div>
                <a href="{{ route('admin.settings') }}" class="text-xs text-yellow-600 hover:underline font-bold flex items-center gap-1 group-hover:text-yellow-700">
                    Ajustar
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" stroke-width="2" />
                    </svg>
                </a>
            </div>
        </div>

        {{-- Card extra: Integrações/API (placeholder) --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col justify-between transition group border-l-4 border-indigo-400 hover:scale-105 hover:shadow-2xl md:col-span-1">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-plug text-indigo-500 text-3xl group-hover:animate-pulse"></i>
                <span class="text-lg font-bold text-gray-800 dark:text-white">Integrações/API</span>
            </div>
            <div class="text-2xl font-extrabold mb-2">Breve</div>
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">Em desenvolvimento</div>
                <a href="#" class="text-xs text-indigo-600 hover:underline font-bold flex items-center gap-1 group-hover:text-indigo-700 opacity-50 cursor-not-allowed">
                    Em breve
                </a>
            </div>
        </div>

        {{-- Card extra: Status do Sistema --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg flex flex-col justify-between transition group border-l-4 border-cyan-400 hover:scale-105 hover:shadow-2xl md:col-span-2">
            <div class="flex items-center gap-3 mb-4">
                <i class="fa-solid fa-signal text-cyan-500 text-3xl group-hover:animate-bounce"></i>
                <span class="text-lg font-bold text-gray-800 dark:text-white">Status do Sistema</span>
            </div>
            <div class="flex items-center gap-4 mb-2">
                <span class="inline-flex items-center text-xs bg-green-100 text-green-600 px-2 py-0.5 rounded font-bold">
                    <i class="fa-solid fa-circle text-green-400 text-xxs mr-1 animate-pulse"></i>
                    Online
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-300">
                    Versão {{ config('app.version', '0.8.8') }}
                </span>
                <span class="text-xs text-gray-500 dark:text-gray-300">
                    Último backup: {{ now()->subDays(1)->format('d/m/Y H:i') }}
                </span>
            </div>
            <div class="flex justify-between items-center w-full">
                <div class="text-sm text-gray-500 dark:text-gray-400">Tudo operando normalmente</div>
            </div>
        </div>
    </div>

    {{-- Adicione mais cards ou estatísticas futuramente --}}
@endsection