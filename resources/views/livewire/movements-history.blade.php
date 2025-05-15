<div class="space-y-4">
  {{-- Filtros --}}
    {{-- Botão de Exportar CSV --}}
  <div class="flex justify-end mb-4">
    <a href="{{ route('movements.export') }}"
       class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
      Exportar CSV Histórico
    </a>
  </div>
  <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-5 gap-4">
    <input type="text" wire:model.debounce.300ms="search"
           placeholder="🔍 Buscar produto…"
           class="px-3 py-2 rounded-lg border focus:ring focus:border-blue-300"/>

    <select wire:model="filterCategory"
            class="px-3 py-2 rounded-lg border focus:ring focus:border-blue-300">
      <option value="">Todas as Categorias</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
      @endforeach
    </select>

    <select wire:model="filterSupplier"
            class="px-3 py-2 rounded-lg border focus:ring focus:border-blue-300">
      <option value="">Todos Fornecedores</option>
      @foreach($suppliers as $sup)
        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
      @endforeach
    </select>

    <input type="date" wire:model="dateStart" placeholder="Início"
           class="px-3 py-2 rounded-lg border focus:ring focus:border-blue-300"/>
    <input type="date" wire:model="dateEnd" placeholder="Fim"
           class="px-3 py-2 rounded-lg border focus:ring focus:border-blue-300"/>
  </div>

  {{-- Tabela Histórico --}}
  <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
    @if($movements->count())
      <table class="min-w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-900">
          <tr class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">
            <th wire:click="sortBy('created_at')" class="px-4 py-2 text-left cursor-pointer select-none">
              Data
              @if($sortField === 'created_at')
                <span class="inline-block align-middle">
                  @if($sortDirection === 'asc') ▲ @else ▼ @endif
                </span>
              @endif
            </th>
            <th class="px-4 py-2 text-left">Produto</th>
            <th class="px-4 py-2 text-right">Qtd.</th>
            <th class="px-4 py-2 text-right">Antes</th>
            <th class="px-4 py-2 text-right">Depois</th>
            <th class="px-4 py-2 text-left">Usuário</th>
            <th class="px-4 py-2 text-left">Tipo</th>
            <th class="px-4 py-2 text-left">Obs.</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
          @foreach($movements as $m)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition">
              <td class="px-4 py-2">{{ $m->created_at->format('d/m/Y H:i') }}</td>
              <td class="px-4 py-2">{{ $m->product->name }}</td>
              <td class="px-4 py-2 text-right">{{ $m->quantity }}</td>
              <td class="px-4 py-2 text-right">{{ $m->before_stock }}</td>
              <td class="px-4 py-2 text-right">{{ $m->after_stock }}</td>
              <td class="px-4 py-2">{{ $m->user->name }}</td>
              <td class="px-4 py-2">{{ ucfirst($m->type) }}</td>
              <td class="px-4 py-2">{{ $m->notes }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="p-6 text-center text-gray-500 dark:text-gray-400">
        Nenhuma movimentação encontrada para os filtros aplicados.
      </div>
    @endif
  </div>

  {{-- Paginação --}}
  <div class="mt-4">
    {{ $movements->links() }}
  </div>
</div>