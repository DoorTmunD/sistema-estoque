@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Novo Usuário</h1>
    <div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="name" class="block font-medium">Nome</label>
                <input id="name" name="name" type="text" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" value="{{ old('name') }}">
            </div>

            <div>
                <label for="email" class="block font-medium">E-mail</label>
                <input id="email" name="email" type="email" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200" value="{{ old('email') }}">
            </div>

            <div>
                <label for="nivel" class="block font-medium">Nível</label>
                <select id="nivel" name="nivel" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
                    <option value="">Selecione...</option>
                    <option value="super-admin" @selected(old('nivel') == 'super-admin')>Super Admin</option>
                    <option value="admin" @selected(old('nivel') == 'admin')>Admin</option>
                    <option value="operador" @selected(old('nivel') == 'operador')>Operador</option>
                </select>
            </div>

            <div>
                <label for="password" class="block font-medium">Senha</label>
                <input id="password" name="password" type="password" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div>
                <label for="password_confirmation" class="block font-medium">Confirme a Senha</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-lg border-gray-300 dark:bg-gray-700 dark:text-gray-200">
            </div>

            <div class="text-right">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow">
                    Salvar Usuário
                </button>
            </div>
        </form>
    </div>
@endsection