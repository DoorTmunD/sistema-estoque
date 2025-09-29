@extends('layouts.app')

@section('header')
    <h2 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">
        Registrar Entrada de Produto
    </h2>
@endsection

@section('content')
<div
  class="bg-white dark:bg-gray-800 shadow rounded-lg p-6"
  x-data="entryForm()"
  x-init="init()"
>
  <form action="{{ route('movements.entry') }}" method="POST" class="space-y-6">
    @csrf

    {{-- Produto --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Produto</label>
      <select
        name="product_id"
        x-model="product_id"
        @change="onProductChange($event)"
        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
        required
      >
        <option value="">-- selecione --</option>
        @foreach(\App\Models\Product::with('supplier')->orderBy('name')->get() as $product)
          <option
              value="{{ $product->id }}"
              data-consumable="{{ $product->is_consumable ? 1 : 0 }}"
          >
            {{ $product->name }} {{ $product->supplier?->name ? '('.$product->supplier->name.')' : '' }}
          </option>
        @endforeach
      </select>
      @error('product_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

      <template x-if="product_id">
        <p class="mt-2 text-xs"
           :class="isConsumable ? 'text-amber-600' : 'text-cyan-600'">
          <template x-if="isConsumable">Consumível: registra <b>consumo em lote</b>.</template>
          <template x-if="!isConsumable">Não consumível: cria <b>itens físicos</b> (opcionalmente com seriais externos).</template>
        </p>
      </template>
    </div>

    {{-- Modo CONSUMÍVEL: quantidade em lote --}}
    <div x-show="isConsumable" x-cloak class="grid md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade</label>
        <input
          type="number" min="1"
          name="qty"
          x-model.number="qty"
          class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
          required
        />
        @error('qty') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custo unitário</label>
        <input
          type="number" step="0.01" min="0"
          name="unit_cost"
          x-model.number="unit_cost"
          class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
          required
        />
        @error('unit_cost') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
        <input
          type="text" readonly
          :value="formatMoney(qty * unit_cost)"
          class="mt-1 block w-full rounded border-gray-200 bg-gray-100 dark:bg-gray-700 dark:text-gray-200"
        />
      </div>
    </div>

    {{-- Modo NÃO CONSUMÍVEL: seriais externos opcionais + custo --}}
    <div x-show="!isConsumable" x-cloak class="space-y-4">
      <div class="grid md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Quantidade
            <span class="text-xs text-gray-500">(se preencher seriais abaixo, calculamos automaticamente)</span>
          </label>
          <input
            type="number" min="1"
            name="qty"
            x-model.number="qty"
            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
            :required="serials_text.trim().length === 0"
          />
          @error('qty') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Custo unitário</label>
          <input
            type="number" step="0.01" min="0"
            name="unit_cost"
            x-model.number="unit_cost"
            class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
            required
          />
          @error('unit_cost') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total</label>
          <input
            type="text" readonly
            :value="formatMoney((effectiveQty()) * unit_cost)"
            class="mt-1 block w-full rounded border-gray-200 bg-gray-100 dark:bg-gray-700 dark:text-gray-200"
          />
        </div>
      </div>

      {{-- Seriais externos (1 por linha) --}}
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
          Seriais externos (opcional) — <span class="text-xs">1 por linha</span>
        </label>
        <textarea
          name="serials_text"
          x-model="serials_text"
          @input="syncQtyFromSerials()"
          rows="5"
          placeholder="EX12345&#10;EX12346&#10;EX12347"
          class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 font-mono text-sm"
        ></textarea>
        @error('serials_text') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        <p class="mt-1 text-xs text-gray-500">
          • Se preencher, criaremos um item por linha e definiremos a <b>quantidade</b> automaticamente.<br>
          • Se deixar vazio, usaremos a quantidade informada acima e geraremos os itens sem serial externo (apenas o
          <b>serial interno</b> será criado automaticamente).
        </p>
      </div>
    </div>

    {{-- Data de aquisição (opcional) --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data de aquisição (opcional)</label>
      <input
        type="date"
        name="acquired_at"
        x-model="acquired_at"
        class="mt-1 block w-full max-w-xs rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
      />
    </div>

    {{-- Observações --}}
    <div>
      <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
      <textarea
        name="notes"
        x-model="notes"
        class="mt-1 block w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200"
        rows="3"
      ></textarea>
    </div>

    {{-- Ações --}}
    <div class="pt-2">
      <button
        type="submit"
        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
      >
        Registrar Entrada
      </button>
    </div>
  </form>
</div>

{{-- Alpine helpers --}}
<script>
function entryForm() {
  return {
    product_id: '{{ old('product_id') }}' || '',
    isConsumable: false,
    qty: Number('{{ old('qty', 1) }}') || 1,
    unit_cost: Number('{{ old('unit_cost', 0) }}') || 0,
    serials_text: @json(old('serials_text', '')),
    acquired_at: '{{ old('acquired_at') }}' || '',
    notes: @json(old('notes', '')),

    init() {
      // se o select já veio preenchido pelo old(), aplicar o consumable baseado no option
      const sel = document.querySelector('select[name="product_id"]');
      if (sel && this.product_id) {
        const opt = sel.querySelector(`option[value="${this.product_id}"]`);
        if (opt) this.isConsumable = opt.dataset.consumable === '1';
      }
      this.syncQtyFromSerials();
    },

    onProductChange(e) {
      const opt = e.target.selectedOptions[0];
      this.isConsumable = opt?.dataset?.consumable === '1';
      // quando troca pra consumível, se tinha seriais preenchidos não influencia
      if (this.isConsumable && !this.qty) this.qty = 1;
    },

    syncQtyFromSerials() {
      const lines = this.serials_text
        .split(/\r?\n/)
        .map(l => l.trim())
        .filter(Boolean);
      if (!this.isConsumable && lines.length > 0) {
        this.qty = lines.length;
      }
    },

    effectiveQty() {
      // não-consumível: se houver seriais, usa seriais; senão usa qty informado
      const lines = this.serials_text
        .split(/\r?\n/)
        .map(l => l.trim())
        .filter(Boolean);
      return lines.length > 0 ? lines.length : (Number(this.qty) || 0);
    },

    formatMoney(v) {
      const n = Number(v || 0);
      return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    },
  };
}
</script>
@endsection
