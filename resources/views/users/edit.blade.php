@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800">Editar Usuário</h2>
@endsection

@section('content')
<div class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow">
    @include('users._form', ['user' => $user])
</div>
@endsection