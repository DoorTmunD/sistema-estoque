@extends('layouts.admin')

@section('content')
    <h1 class="text-2xl font-bold mb-6 flex items-center gap-2">
        <i class="fa-solid fa-users-gear text-blue-500"></i>
        Usuários do Sistema
    </h1>

    <div class="mb-6 flex flex-wrap justify-between items-center gap-2">
        <div class="text-gray-500 dark:text-gray-400 text-sm select-none">
            Total de usuários: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $users->total() }}</span>
        </div>
        @can('create', App\Models\User::class)
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition font-bold group">
                <i class="fa-solid fa-user-plus group-hover:scale-110"></i>
                Novo Usuário
            </a>
        @endcan
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left">Nome</th>
                    <th class="px-4 py-2 text-left">E-mail</th>
                    <th class="px-4 py-2 text-left">Nível</th>
                    <th class="px-4 py-2 text-left">Criado em</th>
                    <th class="px-4 py-2 text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-all group">
                        <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100">
                            <i class="fa-solid fa-user-circle text-gray-400 mr-1"></i>
                            {{ $user->name }}
                        </td>
                        <td class="px-4 py-2">{{ $user->email }}</td>
                        <td class="px-4 py-2">
                            @if($user->nivel === 'super-admin')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-100 text-indigo-700 text-xs font-bold uppercase">
                                    <i class="fa-solid fa-crown text-yellow-400"></i> Super Admin
                                </span>
                            @elseif($user->nivel === 'admin')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-green-100 text-green-700 text-xs font-bold uppercase">
                                    <i class="fa-solid fa-user-shield"></i> Admin
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-gray-200 text-gray-700 text-xs font-bold uppercase">
                                    <i class="fa-solid fa-user"></i> Operador
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-2 text-center">
                            <div class="inline-flex items-center gap-2">
                                @can('update', $user)
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-xs flex items-center gap-1 transition group"
                                       title="Editar usuário">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                        Editar
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    @if(auth()->id() !== $user->id && $user->nivel !== 'super-admin' && auth()->user()->nivel !== 'operador')
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs flex items-center gap-1 transition group"
                                                title="Excluir usuário">
                                                <i class="fa-solid fa-trash"></i>
                                                Excluir
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection