<div class="space-y-8">
  {{-- Ações/Filtros --}}
  <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 flex-1">
      <input type="text" wire:model.debounce.300ms="search"
             placeholder="🔍 Produto, usuário ou nota…"
             class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"/>

      <select wire:model="filterCategory"
              class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm">
        <option value="">Todas as Categorias</option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}">{{ $cat->name }}</option>
        @endforeach
      </select>

      <select wire:model="filterSupplier"
              class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm">
        <option value="">Todos Fornecedores</option>
        @foreach($suppliers as $sup)
          <option value="{{ $sup->id }}">{{ $sup->name }}</option>
        @endforeach
      </select>

      <input type="date" wire:model="dateStart"
             class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"/>
      <input type="date" wire:model="dateEnd"
             class="px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100 shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all text-sm"/>
    </div>

    <div class="mt-3 md:mt-0">
      <a href="{{ route('movements.export') }}"
         class="inline-flex items-center gap-2 px-5 py-2 rounded-xl font-bold bg-cyan-600 text-white shadow hover:bg-cyan-700 transition">
        <i class="fa-solid fa-file-arrow-down"></i>
        Exportar CSV
      </a>
    </div>
  </div>

  {{-- Tabela Histórico --}}
  <div class="overflow-x-auto rounded-xl shadow-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800">
    @if($movements->count())
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800 text-sm">
        <thead class="bg-gradient-to-r from-cyan-100 to-purple-100 dark:from-gray-800 dark:to-gray-900">
          <tr class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">
            <th wire:click="sortBy('created_at')" class="px-4 py-3 text-left cursor-pointer select-none transition hover:text-cyan-600 dark:hover:text-cyan-400">
              Data
              @if($sortField === 'created_at')
                <span class="inline-block align-middle ml-1">
                  @if($sortDirection === 'asc') ▲ @else ▼ @endif
                </span>
              @endif
            </th>
            <th class="px-4 py-3 text-left">Produto</th>
            <th class="px-4 py-3 text-right">Qtd.</th>
            <th class="px-4 py-3 text-right">Antes</th>
            <th class="px-4 py-3 text-right">Depois</th>
            <th class="px-4 py-3 text-left">Usuário</th>
            <th class="px-4 py-3 text-left">Tipo</th>
            <th class="px-4 py-3 text-left">Obs.</th>
            <th class="px-4 py-3 text-left">Anexos</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($movements as $m)
            @php
              $isIn     = in_array($m->ui_kind ?? '', ['in']);     // helper do model
              $badgeCls = $isIn
                  ? 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300'
                  : (in_array($m->ui_kind ?? '', ['out'])
                      ? 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300'
                      : 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300');
              $hasFiles = method_exists($m, 'files') && $m->files && count($m->files);
            @endphp

            <tr class="hover:bg-cyan-50 dark:hover:bg-gray-800 transition group">
              <td class="px-4 py-2 font-mono text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                {{ optional($m->created_at)->format('d/m/Y H:i') }}
              </td>

              <td class="px-4 py-2 font-semibold text-gray-800 dark:text-gray-100">
                {{ $m->product->name ?? '—' }}
              </td>

              <td class="px-4 py-2 text-right text-cyan-700 dark:text-cyan-300">
                {{ $m->qty ?? $m->quantity ?? 0 }}
              </td>

              <td class="px-4 py-2 text-right text-gray-500 dark:text-gray-400">
                {{ $m->before_stock }}
              </td>

              <td class="px-4 py-2 text-right text-gray-500 dark:text-gray-400">
                {{ $m->after_stock }}
              </td>

              <td class="px-4 py-2 text-gray-700 dark:text-gray-200">
                {{ $m->performer->name ?? $m->user->name ?? '—' }}
              </td>

              <td class="px-4 py-2">
                <span class="inline-flex items-center px-2 py-1 rounded font-bold {{ $badgeCls }}">
                  @if($isIn)
                    <i class="fa-solid fa-arrow-down mr-1"></i> {{ $m->type_label ?? 'Entrada' }}
                  @else
                    <i class="fa-solid fa-arrow-up mr-1"></i> {{ $m->type_label ?? 'Saída' }}
                  @endif
                </span>
              </td>

              <td class="px-4 py-2 text-gray-600 dark:text-gray-400">
                @if(!empty($m->notes))
                  <span class="truncate block max-w-[160px]">{{ \Illuminate\Support\Str::limit($m->notes, 32) }}</span>
                @else
                  <span class="text-gray-300 dark:text-gray-600 text-xs">-</span>
                @endif
              </td>

              <td class="px-4 py-2">
                @if($hasFiles)
                  <div class="flex flex-wrap gap-2">
                    @foreach($m->files as $file)
                      <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                         class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100/80 dark:bg-purple-900/80 hover:bg-purple-300 dark:hover:bg-purple-700 rounded text-xs text-purple-900 dark:text-purple-100 shadow-sm transition-all"
                         title="{{ $file->original_name }}">
                        @if(in_array($file->extension, ['jpg','jpeg','png','webp','tiff']))
                          <img src="{{ asset('storage/' . $file->file_path) }}" class="w-7 h-7 object-cover rounded border border-purple-400 shadow" />
                        @elseif($file->extension === 'pdf')
                          <i class="fa-solid fa-file-pdf text-lg text-red-500"></i>
                        @else
                          <span class="font-mono text-xs text-purple-600 dark:text-purple-100">{{ strtoupper($file->extension) }}</span>
                        @endif
                        <span class="truncate max-w-[60px]">{{ \Illuminate\Support\Str::limit($file->original_name, 10) }}</span>
                      </a>
                    @endforeach
                  </div>
                @else
                  <span class="text-gray-400 dark:text-gray-600 text-xs">-</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="p-10 text-center text-gray-500 dark:text-gray-400 text-lg">
        <i class="fa-solid fa-cube text-3xl mb-2 block opacity-40"></i>
        Nenhuma movimentação encontrada para os filtros aplicados.
      </div>
    @endif
  </div>

  {{-- Paginação --}}
  <div class="mt-5 flex justify-end">
    {{ $movements->links() }}
  </div>
</div>
