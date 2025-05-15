<div class="space-y-6">
  {{-- CARDS --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
    <x-dashboard.card title="Produtos" :value="$totalProducts" />
    <x-dashboard.card title="Categorias" :value="$totalCategories" />
    <x-dashboard.card title="Fornecedores" :value="$totalSuppliers" />
    <x-dashboard.card title="Usuários" :value="$totalUsers" />
  </div>

  {{-- GRÁFICOS --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
      <h3 class="text-lg font-semibold mb-4">Estoque vs Ideal</h3>
      {!! $lowStockChart->container() !!}
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
      <h3 class="text-lg font-semibold mb-4">Usuários por Nível</h3>
      {!! $userChart->container() !!}
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
      <h3 class="text-lg font-semibold mb-4">Movimentações do Mês</h3>
      {!! $movementChart->container() !!}
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-6">
      <h3 class="text-lg font-semibold mb-4">Saídas Últimos 6 Meses</h3>
      {!! $salesChart->container() !!}
    </div>
  </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {!! $lowStockChart->script() !!}
    {!! $userChart->script() !!}
    {!! $movementChart->script() !!}
    {!! $salesChart->script() !!}
@endpush