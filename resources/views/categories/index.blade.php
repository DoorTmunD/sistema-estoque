@extends('layouts.app')

@section('title','Categorias')

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2 mb-8">
  <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3 select-none">
    <i class="fa-solid fa-layer-group text-indigo-400"></i>
    Categorias
  </h1>
  <a href="{{ route('categories.create') }}"
     class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-br from-emerald-500 to-cyan-600 hover:from-cyan-600 hover:to-emerald-500 text-white font-bold shadow-lg transition-all text-lg group focus:outline-none">
    <i class="fa-solid fa-plus group-hover:rotate-90 transition-transform"></i>
    Nova Categoria
  </a>
</div>

@if($categories->count())
<div class="overflow-x-auto shadow-xl rounded-2xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
  <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
    <thead>
      <tr>
        <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nome</th>
        <th class="px-6 py-4 text-left text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Criado em</th>
        <th class="px-6 py-4 text-right text-sm font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ações</th>
      </tr>
    </thead>
    <tbody>
      @foreach($categories as $cat)
      <tr class="transition hover:bg-cyan-50 dark:hover:bg-indigo-900/30">
        <td class="px-6 py-4 text-base text-gray-900 dark:text-white font-semibold">{{ $cat->name }}</td>
        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $cat->created_at->format('d/m/Y') }}</td>
        <td class="px-6 py-4 flex justify-end gap-3">
          <a href="{{ route('categories.edit',$cat) }}"
             class="px-3 py-1 rounded bg-indigo-100 hover:bg-indigo-300 dark:bg-indigo-800 dark:hover:bg-indigo-600 text-indigo-700 dark:text-indigo-200 font-semibold shadow transition-all flex items-center gap-1">
            <i class="fa-solid fa-pen-to-square"></i> Editar
          </a>
          <form action="{{ route('categories.destroy',$cat) }}" method="POST" class="inline" onsubmit="return confirm('Remover categoria?')">
            @csrf @method('DELETE')
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
    <p class="mt-2 text-gray-400 text-xl font-semibold">Nenhuma categoria encontrada.</p>
  </div>
@endif
@endsection