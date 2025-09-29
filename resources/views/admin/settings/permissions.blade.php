@extends('layouts.admin')

@section('content')
    @if(auth()->user()->nivel === 'operador')
        <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-8">
            Acesso restrito! Você não possui permissão para gerenciar permissões de usuários.
        </div>
    @else
        <h2 class="text-xl font-bold mb-6">Gerenciamento de Permissões</h2>
        <div class="max-w-xl space-y-6">
            <div>
                <label>Cargo padrão de novos usuários:</label>
                <select class="form-select mt-1 w-full">
                    <option value="operador">Operador</option>
                    <option value="admin">Admin</option>
                    <option value="super-admin">Super Admin</option>
                </select>
            </div>
            <div>
                <label>Permissões rápidas:</label>
                <div class="flex gap-3 mt-2">
                    <button class="px-3 py-1 bg-blue-100 rounded">Visualizar</button>
                    <button class="px-3 py-1 bg-yellow-100 rounded">Editar</button>
                    <button class="px-3 py-1 bg-red-100 rounded">Excluir</button>
                </div>
            </div>
            <div class="mt-8">
                <lottie-player src="https://assets10.lottiefiles.com/private_files/lf30_gydgj1ls.json" background="transparent"  speed="1" style="width: 120px; height: 120px;" loop autoplay></lottie-player>
            </div>
        </div>
    @endif
@endsection