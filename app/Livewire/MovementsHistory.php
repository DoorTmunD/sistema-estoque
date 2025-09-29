<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryMovement;
use App\Models\Category;
use App\Models\Supplier;

class MovementsHistory extends Component
{
    use WithPagination;

    // Filtros
    public string $search = '';
    public string $filterCategory = '';
    public string $filterSupplier = '';
    public ?string $dateStart = null;
    public ?string $dateEnd   = null;

    // Ordenação
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Listas para os dropdowns
    public $categories;
    public $suppliers;

    protected $paginationTheme = 'tailwind';

    // Mantém os filtros/ordenação na URL
    protected $queryString = [
        'search'         => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterSupplier' => ['except' => ''],
        'dateStart'      => ['except' => null],
        'dateEnd'        => ['except' => null],
        'sortField'      => ['except' => 'created_at'],
        'sortDirection'  => ['except' => 'desc'],
    ];

    public function mount(): void
    {
        $this->categories = Category::orderBy('name')->get();
        $this->suppliers  = Supplier::orderBy('name')->get();
    }

    // Volta para a página 1 quando filtros/ordenação mudarem
    public function updating($field): void
    {
        if (in_array($field, [
            'search','filterCategory','filterSupplier','dateStart','dateEnd',
            'sortField','sortDirection'
        ], true)) {
            $this->resetPage();
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        // ⚠️ Removido 'files' do eager load
        $query = InventoryMovement::query()
            ->with([
                // selects mínimos para reduzir payload
                'product:id,name,category_id,supplier_id',
                'product.category:id,name',
                'product.supplier:id,name',
                'item:id,product_id,serial_internal',
                // alias 'user' aponta para performed_by (definido no model)
                'user:id,name',
                'collaborator:id,name',
            ])
            ->when($this->search !== '', function ($q) {
                $s = '%'.trim($this->search).'%';
                $q->where(function ($qq) use ($s) {
                    $qq->where('notes', 'like', $s)
                       ->orWhereHas('product', fn($p) => $p->where('name', 'like', $s))
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', $s))
                       ->orWhereHas('collaborator', fn($u) => $u->where('name', 'like', $s));
                });
            })
            ->when($this->filterCategory !== '', fn($q) =>
                $q->whereHas('product.category', fn($c) => $c->where('id', $this->filterCategory))
            )
            ->when($this->filterSupplier !== '', fn($q) =>
                $q->whereHas('product.supplier', fn($s) => $s->where('id', $this->filterSupplier))
            )
            ->when($this->dateStart, fn($q) =>
                $q->whereDate('created_at', '>=', $this->dateStart)
            )
            ->when($this->dateEnd, fn($q) =>
                $q->whereDate('created_at', '<=', $this->dateEnd)
            );

        // Whitelist de campos ordenáveis para evitar SQL injection
        $allowedSorts = ['created_at', 'qty', 'before_stock', 'after_stock', 'unit_cost', 'total_cost'];
        $field = in_array($this->sortField, $allowedSorts, true) ? $this->sortField : 'created_at';
        $dir   = $this->sortDirection === 'asc' ? 'asc' : 'desc';

        $movements = $query->orderBy($field, $dir)->paginate(15);

        return view('livewire.movements-history', [
            'movements'  => $movements,
            'categories' => $this->categories,
            'suppliers'  => $this->suppliers,
        ]);
    }
}
