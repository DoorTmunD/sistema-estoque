@extends('layouts.admin')

@section('content')
@if(auth()->user()->nivel === 'operador')
    <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-8 shadow">
        <i class="fa-solid fa-ban mr-2"></i>
        Você não possui permissão para acessar os logs de auditoria.
    </div>
@else
    <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
        <i class="fa-solid fa-clipboard-list text-green-500"></i>
        Logs de Auditoria
    </h1>
    <form method="GET" class="flex flex-wrap gap-4 mb-6 items-end bg-white dark:bg-gray-800 rounded-lg p-4 shadow transition"
          x-data="{ showDates: false }">
        <div>
            <label for="user" class="block text-sm font-medium mb-1">Usuário</label>
            <select name="user" id="user" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                <option value="">Todos</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(request('user') == $user->id)>
                        {{ $user->name }} ({{ $user->nivel === 'super-admin' ? 'Super Admin' : ($user->nivel === 'admin' ? 'Admin' : 'Operador') }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="log_name" class="block text-sm font-medium mb-1">Tipo de Log</label>
            <select name="log_name" id="log_name" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                <option value="">Todos</option>
                @foreach($logNames as $logName)
                    <option value="{{ $logName }}" @selected(request('log_name') == $logName)>
                        {{ ucfirst($logName) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="search" class="block text-sm font-medium mb-1">Busca</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   placeholder="Descrição..." class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
        </div>
        <div>
            <label for="per_page" class="block text-sm font-medium mb-1">Por página</label>
            <select name="per_page" id="per_page" class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                @foreach([15,30,50,100] as $op)
                    <option value="{{ $op }}" @selected(request('per_page', 30)==$op)>{{ $op }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col">
            <label class="block text-sm font-medium mb-1 text-white select-none"> </label>
            <button type="button" @click="showDates=!showDates"
                class="flex items-center gap-2 px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg shadow hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                <span>Período</span>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': showDates}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
        <template x-if="showDates">
            <div class="flex gap-4">
                <div>
                    <label for="data_inicio" class="block text-sm font-medium mb-1">Data início</label>
                    <input type="date" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}"
                           class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                </div>
                <div>
                    <label for="data_fim" class="block text-sm font-medium mb-1">Data fim</label>
                    <input type="date" name="data_fim" id="data_fim" value="{{ request('data_fim') }}"
                           class="rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                </div>
            </div>
        </template>
        <button type="submit" class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold shadow transition">
            <i class="fa-solid fa-filter"></i>
            Filtrar
        </button>
        <a href="{{ route('admin.logs.export', request()->all()) }}"
           class="inline-flex items-center gap-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold shadow transition">
            <i class="fa-solid fa-file-csv"></i>
            Exportar CSV
        </a>
    </form>

    <div class="text-gray-500 dark:text-gray-400 text-xs mb-2">
        Exibindo {{ $logs->count() }} de {{ $logs->total() }} logs
        @if($logs->lastPage() > 1)
            – Página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}
        @endif
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg"
         x-data="{ showModal: false, modalLog: null }">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-xs">
            <thead>
            <tr>
                <th class="px-4 py-2 text-left whitespace-nowrap">
                    Data
                    <span class="inline-flex flex-col ml-1 align-middle">
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'asc']) }}"
                           class="text-xs {{ request('sort','desc')=='asc' ? 'text-blue-600 font-bold' : 'text-gray-400' }}"
                           title="Ordem crescente">
                            <svg class="w-3 h-3 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6l5 6H5l5-6z"/></svg>
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['sort' => 'desc']) }}"
                           class="text-xs {{ request('sort','desc')=='desc' ? 'text-blue-600 font-bold' : 'text-gray-400' }}"
                           title="Ordem decrescente">
                            <svg class="w-3 h-3 inline-block" fill="currentColor" viewBox="0 0 20 20"><path d="M10 14l-5-6h10l-5 6z"/></svg>
                        </a>
                    </span>
                </th>
                <th class="px-4 py-2 text-left">Usuário</th>
                <th class="px-4 py-2 text-left">Nível</th>
                <th class="px-4 py-2 text-left">Tipo</th>
                <th class="px-4 py-2 text-left">Descrição</th>
                <th class="px-4 py-2 text-left">Ação Detalhada</th>
                <th class="px-4 py-2 text-left">Registro</th>
                <th class="px-4 py-2 text-center">Info</th>
            </tr>
            </thead>
<tbody x-data="{ showModal: false, selectedLog: null }">
    @forelse($logs as $log)
        <tr class="hover:bg-green-50 dark:hover:bg-green-900/30 transition-all group">
            <td class="px-4 py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
            <td class="px-4 py-2">
                @if($log->causer)
                    <span class="inline-flex items-center gap-1" title="Usuário responsável">
                        <i class="fa-solid fa-user-circle text-gray-400"></i>
                        {{ $log->causer->name }}
                    </span>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            <td class="px-4 py-2">
                @if($log->causer)
                    @if($log->causer->nivel === 'super-admin')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 text-xxs font-bold uppercase" title="Super Admin">
                            <i class="fa-solid fa-crown text-yellow-400"></i> Super Admin
                        </span>
                    @elseif($log->causer->nivel === 'admin')
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-100 text-green-700 text-xxs font-bold uppercase" title="Administrador">
                            <i class="fa-solid fa-user-shield"></i> Admin
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-200 text-gray-700 text-xxs font-bold uppercase" title="Operador">
                            <i class="fa-solid fa-user"></i> Operador
                        </span>
                    @endif
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </td>
            <td class="px-4 py-2">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold" title="Tipo de log">
                    <i class="fa-solid fa-file-lines"></i>
                    {{ ucfirst($log->log_name) }}
                </span>
            </td>
            <td class="px-4 py-2">{{ $log->description }}</td>
            <td class="px-4 py-2">
                @php
                    $event = $log->event ?? '';
                    $attributes = $log->properties['attributes'] ?? null;
                    $details = '';
                    $maxLen = 110;
                    if (!empty($event)) {
                        $details .= ucfirst($event);
                    }
                    if ($attributes) {
                        $details .= ' — ';
                        // Exibe só os primeiros 110 caracteres
                        $short = collect($attributes)->map(function($val, $key) {
                            return "<span class='inline-block font-mono text-xxs bg-gray-100 dark:bg-gray-700 px-1 py-0.5 rounded mr-1'>" . e($key) . ': ' . e(Str::limit($val, 30)) . "</span>";
                        })->implode(' ');
                        $full = collect($attributes)->map(function($val, $key) {
                            return "<div class='block font-mono text-xs bg-gray-50 dark:bg-gray-800 px-1 py-0.5 rounded mb-1'>" . e($key) . ': ' . e($val) . "</div>";
                        })->implode('');
                    }
                @endphp
                @if(!empty($attributes) && strlen(strip_tags($short ?? '')) > $maxLen)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-200 font-bold cursor-pointer"
                          x-data="{ open: false }"
                          @click="open = !open"
                          title="Clique para ver completo">
                        <i class="fa-solid fa-circle-info"></i>
                        {!! Str::limit(strip_tags($short), $maxLen, '...') !!}
                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <div x-show="open" @click.away="open = false"
                             class="absolute z-50 mt-2 p-4 bg-white dark:bg-gray-900 border rounded shadow-lg max-w-lg max-h-96 overflow-y-auto"
                             style="min-width:200px;">
                            <div class="mb-2 font-bold text-sm">Ação Detalhada Completa</div>
                            {!! $full ?? '' !!}
                        </div>
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-200 font-bold" title="Ação detalhada">
                        <i class="fa-solid fa-circle-info"></i>
                        {!! $short ?? ($details ?: '-') !!}
                    </span>
                @endif
            </td>
            <td class="px-4 py-2">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                    <i class="fa-solid fa-database"></i>
                    {{ class_basename($log->subject_type) }}
                </span>
            </td>
            <!-- Coluna Info -->
            <td class="px-4 py-2 text-center">
                <!-- Botão Info: abre o modal (NUNCA deve sumir) -->
                <button
                    @click="selectedLog = {{ $log->id }}; showModal = true"
                    class="text-blue-600 hover:text-blue-800 focus:outline-none"
                    title="Mais informações"
                    type="button"
                >
                    <i class="fa-solid fa-circle-info"></i>
                </button>
                <!-- Modal Alpine.js de detalhes -->
                <template x-if="showModal && selectedLog === {{ $log->id }}">
                    <div
                        class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center"
                        style="backdrop-filter: blur(2px);"
                        @click.self="showModal = false"
                        @keydown.escape.window="showModal = false"
                        tabindex="0"
                    >
                        <div class="bg-white dark:bg-gray-900 p-6 rounded-xl max-w-lg w-full relative shadow-2xl">
                            <!-- Botão X para fechar -->
                            <button class="absolute top-2 right-2 text-gray-400 hover:text-red-500"
                                @click="showModal = false"
                                type="button"
                            >
                                <i class="fa-solid fa-times"></i>
                            </button>
                            <h2 class="text-lg font-bold mb-3 flex items-center gap-2">
                                <i class="fa-solid fa-circle-info text-blue-500"></i>
                                Detalhes do Log
                            </h2>
                            <div class="mb-2"><b>Data:</b> {{ $log->created_at->format('d/m/Y H:i') }}</div>
                            <div class="mb-2"><b>Usuário:</b> {{ $log->causer->name ?? '-' }}</div>
                            <div class="mb-2"><b>Nível:</b> {{ $log->causer->nivel ?? '-' }}</div>
                            <div class="mb-2"><b>IP:</b> {{ $log->properties['ip'] ?? '-' }}</div>
                            <div class="mb-2"><b>User-Agent:</b> <span class="break-all">{{ $log->properties['user_agent'] ?? '-' }}</span></div>
                            <div class="mb-2"><b>Cidade:</b> {{ $log->properties['city'] ?? '-' }}</div>
                            <div class="mb-2"><b>País:</b> {{ $log->properties['country'] ?? '-' }}</div>
                            <div class="mb-2"><b>Hostname:</b> {{ $log->properties['hostname'] ?? '-' }}</div>
                            <div class="mt-4 bg-gray-100 dark:bg-gray-800 p-2 rounded text-xs overflow-auto max-h-32">
                                <pre>{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    </div>
                </template>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="text-center text-gray-500 py-6">Nenhum log encontrado.</td>
        </tr>
    @endforelse
</tbody>
        </table>
        <div class="p-4">
            {{ $logs->appends(request()->all())->links() }}
        </div>
    </div>
@endif
@endsection