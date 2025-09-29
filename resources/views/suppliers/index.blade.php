@extends('layouts.app')

@section('title', 'Fornecedores')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-8">
    <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3 select-none">
        <i class="fa-solid fa-truck-field text-green-400"></i>
        Fornecedores
    </h1>
    <a href="{{ route('suppliers.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 hover:from-emerald-600 hover:to-green-500 text-white font-bold shadow-lg transition-all text-lg group focus:outline-none">
        <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform"></i>
        Novo Fornecedor
    </a>
</div>

@if($suppliers->count())
<div class="overflow-x-auto shadow-xl rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead>
            <tr>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nome</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">E-mail</th>
                <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Telefone</th>
                <th class="px-6 py-4 text-right text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ações</th>
            </tr>
        </thead>
        <tbody>
        @foreach($suppliers as $sup)
            <tr class="transition hover:bg-emerald-50 dark:hover:bg-green-900/30">
                <td class="px-6 py-4 text-base text-gray-900 dark:text-white font-semibold">{{ $sup->name }}</td>
                <td class="px-6 py-4 text-gray-500 dark:text-gray-300">{{ $sup->email }}</td>
                <td class="px-6 py-4 text-gray-500 dark:text-gray-300">{{ $sup->phone }}</td>
                <td class="px-6 py-4 flex justify-end gap-3">
                    <a href="{{ route('suppliers.edit', $sup) }}"
                       class="px-3 py-1 rounded bg-green-100 hover:bg-green-300 dark:bg-green-800 dark:hover:bg-green-600 text-green-700 dark:text-green-200 font-semibold shadow transition-all flex items-center gap-1">
                        <i class="fa-solid fa-pen-to-square"></i> Editar
                    </a>
                    <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="inline" onsubmit="return confirm('Remover fornecedor?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 rounded bg-red-100 hover:bg-red-400 dark:bg-red-800 dark:hover:bg-red-600 text-red-700 dark:text-red-200 font-semibold shadow transition-all flex items-center gap-1">
                            <i class="fa-solid fa-trash"></i> Excluir
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@else
    <div class="flex flex-col items-center justify-center py-24 animate__animated animate__fadeIn">
        <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <circle cx="24" cy="24" r="22" stroke-width="2"/>
            <path stroke-width="2" d="M24 16v10m0 6h.01"/>
        </svg>
        <p class="mt-2 text-gray-400 text-xl font-semibold">Nenhum fornecedor encontrado.</p>
    </div>
@endif
@endsection