@php
    // Espera: $product, $items, $users
    $badge = fn($st) => match($st){
        \App\Models\ProductItem::STATUS_AVAILABLE => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        \App\Models\ProductItem::STATUS_LOANED    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        \App\Models\ProductItem::STATUS_CONSUMED  => 'bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-300',
        \App\Models\ProductItem::STATUS_BROKEN,
        \App\Models\ProductItem::STATUS_LOST      => 'bg-slate-200 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    };
@endphp

<div class="p-4 bg-white/70 dark:bg-gray-900/70 border border-gray-200 dark:border-gray-700 rounded-xl">
  <div class="mb-3 text-sm text-slate-700 dark:text-slate-300">
    <span class="mr-4">Disponíveis: <b>{{ $items->where('status', \App\Models\ProductItem::STATUS_AVAILABLE)->count() }}</b></span>
    <span class="mr-4">Emprestados: <b>{{ $items->where('status', \App\Models\ProductItem::STATUS_LOANED)->count() }}</b></span>
    <span>Consumidos: <b>{{ $items->whereIn('status', [\App\Models\ProductItem::STATUS_CONSUMED,\App\Models\ProductItem::STATUS_BROKEN,\App\Models\ProductItem::STATUS_LOST])->count() }}</b></span>
  </div>

  @if($items->isEmpty())
    <p class="text-sm text-slate-500 dark:text-slate-400">Nenhum item cadastrado para este produto.</p>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm rounded-md overflow-hidden">
        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
          <tr>
            <th class="py-2 px-3 text-left">Serial Interno</th>
            <th class="py-2 px-3 text-left">Serial Externo</th>
            <th class="py-2 px-3 text-left">Status</th>
            <th class="py-2 px-3 text-left">Colaborador</th>
            <th class="py-2 px-3 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
          @foreach ($items as $row)
            <tr class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-900 dark:even:bg-gray-800">
              <td class="py-2 px-3 font-mono">{{ $row->serial_internal ?? '—' }}</td>
              <td class="py-2 px-3">{{ $row->serial_external ?: '—' }}</td>
              <td class="py-2 px-3">
                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $badge($row->status) }}">
                  {{ strtoupper($row->status) }}
                </span>
              </td>
              <td class="py-2 px-3">{{ $row->holder?->name ?? '—' }}</td>
              <td class="py-2 px-3 text-right space-x-2">

                {{-- EMPRESTAR --}}
                @if(!$product->is_consumable && $row->status === \App\Models\ProductItem::STATUS_AVAILABLE)
                  <details class="inline-block">
                    <summary class="list-none px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-700 text-white text-xs inline-block cursor-pointer">
                      Emprestar
                    </summary>
                    <form method="POST" action="{{ route('inventory.loan.out', $row) }}"
                          class="mt-2 p-3 border rounded-md bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                      @csrf
                      <div class="mb-2">
                        <select name="collaborator_id" class="w-64 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900" required>
                          <option value="">Selecione o colaborador…</option>
                          @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                          @endforeach
                        </select>
                      </div>
                      <div class="mb-2">
                        <textarea name="notes" rows="2" placeholder="Observação (opcional)"
                                  class="w-64 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-900"></textarea>
                      </div>
                      <button class="px-3 py-1 rounded bg-indigo-600 hover:bg-indigo-700 text-white text-xs">Confirmar</button>
                    </form>
                  </details>
                @endif

                {{-- DEVOLVER --}}
                @if(!$product->is_consumable && $row->status === \App\Models\ProductItem::STATUS_LOANED)
                  <form method="POST" action="{{ route('inventory.loan.return', $row) }}" class="inline">
                    @csrf
                    <button class="px-3 py-1 rounded bg-emerald-600 hover:bg-emerald-700 text-white text-xs"
                            onclick="return confirm('Confirmar devolução deste item?')">
                      Devolver
                    </button>
                  </form>
                @endif

                {{-- CONSUMIR (não-consumível) --}}
                @if($row->status === \App\Models\ProductItem::STATUS_AVAILABLE)
                  <form method="POST" action="{{ route('inventory.consume', $row) }}" class="inline">
                    @csrf
                    <button class="px-3 py-1 rounded bg-rose-600 hover:bg-rose-700 text-white text-xs"
                            onclick="return confirm('Registrar consumo deste item?')">
                      Consumir
                    </button>
                  </form>
                @endif

                {{-- AJUSTAR --}}
                <details class="inline-block">
                  <summary class="px-3 py-1 rounded bg-slate-100 dark:bg-slate-700 text-xs cursor-pointer">Ajustar</summary>
                  <form method="POST" action="{{ route('inventory.adjust', $row) }}" class="inline">
                    @csrf
                    <select name="status" class="border rounded px-2 py-1 text-xs dark:bg-gray-900 dark:border-gray-600">
                      @foreach(['AVAILABLE','LOANED','CONSUMED','BROKEN','LOST'] as $st)
                        <option value="{{ $st }}" @selected($st === ($row->status ?? ''))>{{ $st }}</option>
                      @endforeach
                    </select>
                    <input type="text" name="serial_external" value="{{ $row->serial_external }}"
                           placeholder="Serial externo"
                           class="border rounded px-2 py-1 text-xs dark:bg-gray-900 dark:border-gray-600">
                    <input type="text" name="notes" placeholder="Motivo"
                           class="border rounded px-2 py-1 text-xs dark:bg-gray-900 dark:border-gray-600">
                    <button class="px-3 py-1 rounded bg-slate-800 hover:bg-slate-900 text-white text-xs">Salvar</button>
                  </form>
                </details>

              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
