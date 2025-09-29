@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        Editar Produto
    </h2>
@endsection

@section('content')
<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 max-w-2xl mx-auto">
    @include('products._form', ['product' => $product, 'categories' => $categories, 'suppliers' => $suppliers])
</div>
@endsection
