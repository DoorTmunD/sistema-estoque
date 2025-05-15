@extends('layouts.app')

@section('title', 'Fornecedores')

@section('content')
<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Fornecedores</h1>
    <a href="{{ route('suppliers.create') }}"
       class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
       + Novo Fornecedor
    </a>
</div>

<table class="w-full bg-white rounded shadow overflow-hidden">
    <thead class="bg-gray-200 text-left">
        <tr>
            <th class="px-4 py-2">Nome</th>
            <th class="px-4 py-2">E-mail</th>
            <th class="px-4 py-2">Telefone</th>
            <th class="px-4 py-2">Ações</th>
        </tr>
    </thead>
    <tbody>
    @foreach($suppliers as $sup)
        <tr class="border-t">
            <td class="px-4 py-2">{{ $sup->name }}</td>
            <td class="px-4 py-2">{{ $sup->email }}</td>
            <td class="px-4 py-2">{{ $sup->phone }}</td>
            <td class="px-4 py-2 space-x-2">
                <a href="{{ route('suppliers.edit', $sup) }}" class="text-blue-600">Editar</a>
                <form action="{{ route('suppliers.destroy', $sup) }}" method="POST" class="inline" onsubmit="return confirm('Remover fornecedor?')">
                    @csrf
                    @method('DELETE')
                    <button class="text-red-600">Excluir</button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="mt-4">
    {{ $suppliers->links() }}
</div>
@endsection