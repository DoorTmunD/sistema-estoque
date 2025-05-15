@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
        Histórico de Movimentações
    </h2>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <livewire:movements-history />
    </div>
@endsection