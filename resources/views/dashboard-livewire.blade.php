@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
        {{ __('GRÁFICOS') }}
    </h2>
@endsection

@section('content')
    @livewire('dashboard', [
        // período com fallback e leitura do query param ?period
        'periodDays'          => ($periodDays ?? (int) request('period', 30)),

        // cards
        'totalStock'          => $totalStock          ?? 0,
        'categoryCount'       => $categoryCount       ?? 0,
        'supplierCount'       => $supplierCount       ?? 0,
        'belowIdeal'          => $belowIdeal          ?? 0,
        'produtosAbaixoIdeal' => $produtosAbaixoIdeal ?? [],

        // gráfico de movimentação
        'movLabels'           => $movLabels  ?? [],
        'movEntries'          => $movEntries ?? [],
        'movExits'            => $movExits   ?? [],

        // donut por categoria e top produtos
        'stockPerCategory'    => $stockPerCategory   ?? [],
        'topProductsByValue'  => $topProductsByValue ?? [],
    ])
@endsection
