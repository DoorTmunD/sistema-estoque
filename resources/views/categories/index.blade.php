@extends('layouts.app')

@section('title','Categorias')

@section('content')
<div class="flex justify-between items-center mb-4">
  <h1 class="text-2xl font-bold">Categorias</h1>
  <a href="{{ route('categories.create') }}"
     class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
    + Nova
  </a>
</div>

<table class="w-full bg-white rounded shadow overflow-hidden">
  <thead class="bg-gray-200 text-left">
    <tr>
      <th class="px-4 py-2">Nome</th>
      <th class="px-4 py-2">Criado em</th>
      <th class="px-4 py-2">Ações</th>
    </tr>
  </thead>
  <tbody>
    @foreach($categories as $cat)
    <tr class="border-t">
      <td class="px-4 py-2">{{ $cat->name }}</td>
      <td class="px-4 py-2">{{ $cat->created_at->format('d/m/Y') }}</td>
      <td class="px-4 py-2 space-x-2">
        <a href="{{ route('categories.edit',$cat) }}" class="text-blue-600">Editar</a>
        <form action="{{ route('categories.destroy',$cat) }}" method="POST" class="inline" onsubmit="return confirm('Remover?')">
          @csrf @method('DELETE')
          <button class="text-red-600">Excluir</button>
        </form>
      </td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="mt-4">
  {{ $categories->links() }}
</div>
@endsection