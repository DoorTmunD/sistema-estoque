@extends('layouts.app')

@section('header')
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Lista de Usuários</h2>
@endsection

@section('content')
<div class="max-w-5xl mx-auto bg-white rounded-2xl shadow p-8">
    <div class="flex justify-end mb-6">
        <a
            href="{{ route('users.create') }}"
            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 active:bg-blue-800 transition"
        >
            + Novo Usuário
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nível</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucwords(str_replace('-', ' ', $user->nivel)) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a
                                href="{{ route('users.edit', $user) }}"
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Editar
                            </a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="text-red-600 hover:text-red-800"
                                    onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                >
                                    Excluir
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
@endsection
