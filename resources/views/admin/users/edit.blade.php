@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Editar Usuário</h1>
    <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block font-medium">Nome</label>
                <input id="name" name="name" type="text" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" value="{{ old('name', $user->name) }}">
            </div>

            <div>
                <label for="email" class="block font-medium">E-mail</label>
                <input id="email" name="email" type="email" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" value="{{ old('email', $user->email) }}">
            </div>

            <div>
                <label for="nivel" class="block font-medium">Nível</label>
                <select id="nivel" name="nivel" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200"
                  @if($user->id === auth()->id() && $user->nivel === 'super-admin') disabled @endif>
                    <option value="">Selecione...</option>
                    <option value="super-admin" @selected(old('nivel', $user->nivel) == 'super-admin')>Super Admin</option>
                    <option value="admin" @selected(old('nivel', $user->nivel) == 'admin')>Admin</option>
                    <option value="operador" @selected(old('nivel', $user->nivel) == 'operador')>Operador</option>
                </select>
                @if($user->id === auth()->id() && $user->nivel === 'super-admin')
                  <p class="text-xs text-yellow-600 mt-1">Você é o único Super Admin. Não pode alterar o próprio nível.</p>
                @endif
            </div>

            <div>
                <label for="password" class="block font-medium">Nova Senha (opcional)</label>
                <input id="password" name="password" type="password" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="password_confirmation" class="block font-medium">Confirme a Nova Senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div class="text-right">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
                    Atualizar Usuário
                </button>
            </div>
        </form>
    </div>
@endsection