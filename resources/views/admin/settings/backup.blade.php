@extends('layouts.admin')

@section('content')
    @if(auth()->user()->nivel === 'operador')
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-8">
            Acesso restrito! Você não possui permissão para realizar backup do sistema.
        </div>
    @else
        <h2 class="text-xl font-bold mb-6">Backup do Sistema</h2>
        <div class="max-w-xl space-y-6">
            <div>
                <button class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Gerar Backup Agora</button>
            </div>
            <div>
                <span class="text-gray-600">Último backup: 25/05/2025 17:00</span>
            </div>
            <div>
                <button class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">Exportar para AWS S3 <span class="ml-2">🚀</span></button>
            </div>
            <div class="mt-8">
                <lottie-player src="https://assets6.lottiefiles.com/private_files/lf30_m6j5igxb.json" background="transparent"  speed="1" style="width: 120px; height: 120px;" loop autoplay></lottie-player>
            </div>
        </div>
    @endif
@endsection