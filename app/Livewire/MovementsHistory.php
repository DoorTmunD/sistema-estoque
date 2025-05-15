<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryMovement;
use App\Models\Category;
use App\Models\Supplier;
use Carbon\Carbon;

class MovementsHistory extends Component
{
    use WithPagination;

    // filtros
    public $search         = '';
    public $filterCategory = '';
    public $filterSupplier = '';
    public $dateStart      = null;
    public $dateEnd        = null;

    // ordenação
    public $sortField     = 'created_at';
    public $sortDirection = 'desc';

    // listas para dropdowns
    public $categories;
    public $suppliers;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->categories = Category::orderBy('name')->get();
        $this->suppliers  = Supplier::orderBy('name')->get();
    }

    // Sempre que um filtro ou ordenação mudar, volta para a página 1
    public function updating($field)
    {
        if (in_array($field, [
            'search','filterCategory','filterSupplier','dateStart','dateEnd',
            'sortField','sortDirection'
        ])) {
            $this->resetPage();
        }
    }

    /**
     * Alterna asc/desc ao clicar no cabeçalho, ou muda campo de ordenação.
     */
    public function sortBy(string $field)
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
        $query = InventoryMovement::with(['product.category','product.supplier','user']);

        if ($this->search) {
            $query->whereHas('product', fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
            );
        }

        if ($this->filterCategory) {
            $query->whereHas('product', fn($q) =>
                $q->where('category_id', $this->filterCategory)
            );
        }

        if ($this->filterSupplier) {
            $query->whereHas('product', fn($q) =>
                $q->where('supplier_id', $this->filterSupplier)
            );
        }

        if ($this->dateStart) {
            $start = Carbon::parse($this->dateStart)->startOfDay();
            $query->where('created_at', '>=', $start);
        }

        if ($this->dateEnd) {
            $end = Carbon::parse($this->dateEnd)->endOfDay();
            $query->where('created_at', '<=', $end);
        }

        // aplica ordenação dinâmica
        $movements = $query
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.movements-history', [
            'movements' => $movements,
        ]);
    }
}