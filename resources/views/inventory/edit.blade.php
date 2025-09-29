@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Editar Registro de Estoque</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
    @include('inventory._form', ['inventory' => $inventory])
</div>
@endsection
