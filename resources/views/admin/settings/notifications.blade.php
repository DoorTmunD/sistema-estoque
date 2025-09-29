@extends('layouts.admin')

@section('content')
    @if(auth()->user()->nivel === 'operador')
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-8">
            Acesso restrito! Você não possui permissão para configurar notificações do sistema.
        </div>
    @else
        <h2 class="text-xl font-bold mb-6">Configuração de Notificações</h2>
        <div class="max-w-xl space-y-6">
            <div>
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox" checked>
                    <span class="ml-2">Ativar notificações por e-mail</span>
                </label>
            </div>
            <div>
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox">
                    <span class="ml-2">Ativar notificações in-app (toast)</span>
                </label>
            </div>
            <div class="mt-8">
                <lottie-player src="https://assets7.lottiefiles.com/packages/lf20_0yfsb3a1.json" background="transparent"  speed="1" style="width: 120px; height: 120px;" loop autoplay></lottie-player>
            </div>
        </div>
    @endif
@endsection