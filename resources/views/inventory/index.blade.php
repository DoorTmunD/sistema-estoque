@extends('layouts.app')

@section('header')
  <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Estoque</h2>
@endsection

@section('content')
<div class="max-w-6xl mx-auto bg-white/90 dark:bg-gray-900/90 border border-gray-100 dark:border-gray-800 rounded-2xl shadow p-6">

  <div class="flex items-center justify-between mb-6">
    <a href="{{ route('inventory.export') }}"
       class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold">
      Exportar CSV
    </a>

    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200">Lista de Estoque</h3>

    <a href="{{ route('inventory.create') }}"
       class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-semibold">
      + Novo Registro de Estoque
    </a>
  </div>

  @if(session('success'))
    <div class="mb-4 text-sm px-3 py-2 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="mb-4 text-sm px-3 py-2 rounded bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200">{{ session('error') }}</div>
  @endif

  @if($inventories->count())
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-800">
        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
          <tr>
            <th class="px-4 py-3 w-10"></th>
            <th class="px-4 py-3 text-left">Produto</th>
            <th class="px-4 py-3 text-left hidden md:table-cell">Categoria</th>
            <th class="px-4 py-3 text-left hidden md:table-cell">Fornecedor</th>
            <th class="px-4 py-3 text-right">Disponíveis</th>
            <th class="px-4 py-3 text-right hidden sm:table-cell">Emprestados</th>
            <th class="px-4 py-3 text-right hidden sm:table-cell">Consumidos</th>
            <th class="px-4 py-3 text-right">Ideal</th>
            <th class="px-4 py-3 text-center">Ações</th>
          </tr>
        </thead>

        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
          @foreach($inventories as $inv)
            @php
              $p = $inv->product;
              // Usa accessors do model (com fallback para snapshot dos consumíveis)
              $available = $p?->available_count ?? (int) $inv->qnt_estoque;
              $loaned    = $p?->loaned_count ?? 0;
              $consumed  = $p?->consumed_count ?? 0;
            @endphp

            {{-- Linha principal --}}
            <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
              <td class="px-4 py-3">
                <button
                  class="toggle-row inline-flex items-center justify-center w-7 h-7 rounded hover:bg-gray-200/60 dark:hover:bg-gray-700/60 text-gray-600 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                  aria-label="Expandir itens do produto"
                  aria-expanded="false"
                  aria-controls="row-items-{{ $p->id }}"
                  data-product-id="{{ $p->id }}"
                  data-open="0">
                  {{-- setinha via SVG (gira quando aberto) --}}
                  <svg class="w-4 h-4 transition-transform duration-150" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M7 5l6 5-6 5V5z" clip-rule="evenodd" />
                  </svg>
                </button>
              </td>
              <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-100">{{ $p->name }}</td>
              <td class="px-4 py-3 hidden md:table-cell">{{ $p->category->name ?? '—' }}</td>
              <td class="px-4 py-3 hidden md:table-cell">{{ $p->supplier->name ?? '—' }}</td>
              <td class="px-4 py-3 text-right">{{ $available }}</td>
              <td class="px-4 py-3 text-right hidden sm:table-cell">{{ $loaned }}</td>
              <td class="px-4 py-3 text-right hidden sm:table-cell">{{ $consumed }}</td>
              <td class="px-4 py-3 text-right">{{ $inv->qnt_ideal }}</td>
              <td class="px-4 py-3 text-center">
                <a href="{{ route('inventory.edit', $inv) }}"
                   class="inline-flex items-center px-2 py-1 rounded bg-yellow-500 hover:bg-yellow-600 text-white">Editar</a>
                <form action="{{ route('inventory.destroy', $inv) }}" method="POST" class="inline"
                      onsubmit="return confirm('Excluir este registro de estoque?')">
                  @csrf @method('DELETE')
                  <button class="inline-flex items-center px-2 py-1 rounded bg-red-600 hover:bg-red-700 text-white">Excluir</button>
                </form>
              </td>
            </tr>

            {{-- Linha expandida (itens + ações) --}}
            <tr class="hidden" id="row-items-{{ $p->id }}">
              <td colspan="9" class="px-0 py-0">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800">
                  {{-- Skeleton inicial; substituído pelo HTML da parcial --}}
                  <div id="items-container-{{ $p->id }}" class="space-y-2">
                    <div class="h-4 rounded bg-gray-200 dark:bg-gray-700 animate-pulse w-40"></div>
                    <div class="h-28 rounded bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                  </div>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-6 flex justify-end">
      {{ $inventories->links() }}
    </div>
  @else
    <div class="py-12 text-center text-gray-400 dark:text-gray-500">Nenhum registro de estoque.</div>
  @endif
</div>

{{-- Script inline para não depender de @stack("scripts") --}}
<script>
(function(){
  const loaded = new Set();

  function rotateChevron(btn, open){
    btn.dataset.open = open ? '1' : '0';
    const svg = btn.querySelector('svg');
    if(svg){
      svg.style.transform = open ? 'rotate(90deg)' : 'rotate(0deg)';
    }
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }

  function loadItems(productId){
    const url = "{{ route('inventory.items.partial', ':id') }}".replace(':id', productId);
    const target = document.getElementById('items-container-'+productId);
    if(!target) return;

    // mantém skeleton se for a primeira carga
    fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' }})
      .then(r => r.text())
      .then(html => target.innerHTML = html)
      .catch(() => target.innerHTML = '<div class="text-sm text-rose-600 dark:text-rose-300">Falha ao carregar itens.</div>');
  }

  function toggleRow(btn){
    const id  = btn.dataset.productId;
    const row = document.getElementById('row-items-'+id);
    if(!row) return;

    const willOpen = row.classList.contains('hidden');
    row.classList.toggle('hidden');
    rotateChevron(btn, willOpen);

    if(willOpen && !loaded.has(id)){
      loaded.add(id);
      loadItems(id);
    }
  }

  // Clique + teclado (Acessibilidade)
  document.querySelectorAll('.toggle-row').forEach(btn => {
    btn.addEventListener('click', () => toggleRow(btn));
    btn.addEventListener('keydown', (e) => {
      if(e.key === 'Enter' || e.key === ' '){
        e.preventDefault();
        toggleRow(btn);
      }
    });
  });
})();
</script>
@endsection
