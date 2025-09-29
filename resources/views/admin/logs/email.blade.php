@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
            <i class="fa-solid fa-paper-plane text-cyan-400 animate-bounce"></i>
            Logs de E-mail enviados
        </h1>

        <div class="overflow-x-auto bg-white dark:bg-gray-900/80 rounded-2xl shadow-lg border border-cyan-600/20">
            <table class="min-w-full text-xs">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Data/Hora</th>
                        <th class="px-4 py-2">Destinatário</th>
                        <th class="px-4 py-2">Assunto</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Ação</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($logs as $log)
                    <tr class="hover:bg-cyan-50 dark:hover:bg-cyan-900 transition-all">
                        <td class="px-4 py-2">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-2 font-mono">{{ $log->to }}</td>
                        <td class="px-4 py-2">{{ $log->subject }}</td>
                        <td class="px-4 py-2">
                            @if($log->success)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-100 text-green-700 font-bold"><i class="fa-solid fa-circle-check"></i> Enviado</span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-red-100 text-red-700 font-bold"><i class="fa-solid fa-circle-xmark"></i> Erro</span>
                            @endif
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('admin.logs.email.show', $log->id) }}" class="text-cyan-600 hover:underline font-bold">Ver +</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-400 py-8">Nenhum log encontrado.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $logs->links() }}
        </div>
    </div>
@endsection