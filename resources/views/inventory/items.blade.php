@extends('layouts.app')

@section('header')
  <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
    Itens de {{ $product->name }}
  </h2>
@endsection

@section('content')
<div class="max-w-6xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow p-6">
  <div class="mb-4 text-sm text-slate-600 dark:text-slate-300">
    <span class="mr-4">Disponíveis: <b>{{ $product->available_count ?? 0 }}</b></span>
    <span class="mr-4">Emprestados: <b>{{ $product->loaned_count ?? 0 }}</b></span>
    <span>Consumidos: <b>{{ $product->consumed_count ?? 0 }}</b></span>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-left">
      <thead>
        <tr class="text-gray-500 dark:text-gray-400 text-sm">
          <th class="px-3 py-2">Serial Interno</th>
          <th class="px-3 py-2">Serial Externo</th>
          <th class="px-3 py-2">Status</th>
          <th class="px-3 py-2">Colaborador</th>
          <th class="px-3 py-2 text-right">Ações</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
      @forelse($items as $item)
        @php
          $map = [
            \App\Models\ProductItem::STATUS_AVAILABLE => ['Disponível','bg-green-100 text-green-700'],
            \App\Models\ProductItem::STATUS_LOANED    => ['Emprestado','bg-amber-100 text-amber-700'],
            \App\Models\ProductItem::STATUS_CONSUMED  => ['Consumido','bg-gray-200 text-gray-700'],
            \App\Models\ProductItem::STATUS_BROKEN    => ['Quebrado','bg-red-100 text-red-700'],
            \App\Models\ProductItem::STATUS_LOST      => ['Perdido','bg-red-100 text-red-700'],
          ];
          [$label,$cls] = $map[$item->status] ?? [$item->status,'bg-slate-100 text-slate-700'];
        @endphp
        <tr>
          <td class="px-3 py-3 font-mono">{{ $item->serial_internal }}</td>
          <td class="px-3 py-3 font-mono">{{ $item->serial_external ?: '—' }}</td>
          <td class="px-3 py-3">
            <span class="px-2 py-1 rounded text-xs {{ $cls }}">{{ $label }}</span>
          </td>
          <td class="px-3 py-3">{{ $item->holder?->name ?: '—' }}</td>

          <td class="px-3 py-3 text-right space-x-2">
            {{-- Ações só fazem sentido para NÃO-consumíveis --}}
            @if(!($product->is_consumable ?? false))
              {{-- Emprestar (somente se disponível) --}}
              @if($item->status === \App\Models\ProductItem::STATUS_AVAILABLE)
                <form class="inline" method="POST" action="{{ route('items.loan', $item) }}">
                  @csrf
                  <select name="collaborator_id" class="border rounded px-2 py-1 text-sm">
                    <option value="">Colaborador…</option>
                    @foreach(\App\Models\User::orderBy('name')->get() as $u)
                      <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                  </select>
                  <button class="px-3 py-1 rounded bg-indigo-600 text-white text-sm">Emprestar</button>
                </form>

                <form class="inline" method="POST" action="{{ route('items.consume', $item) }}">
                  @csrf
                  <button class="px-3 py-1 rounded bg-rose-600 text-white text-sm"
                          onclick="return confirm('Consumir este item?')">Consumir</button>
                </form>
              @endif

              {{-- Devolver (se emprestado) --}}
              @if($item->status === \App\Models\ProductItem::STATUS_LOANED)
                <form class="inline" method="POST" action="{{ route('items.return', $item) }}">
                  @csrf
                  <button class="px-3 py-1 rounded bg-emerald-600 text-white text-sm">Devolver</button>
                </form>
              @endif
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="5" class="px-3 py-12 text-center text-gray-400">Nenhum item encontrado.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->links() }}
  </div>
</div>
@endsection
