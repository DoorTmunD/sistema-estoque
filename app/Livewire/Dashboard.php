<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\ProductItem;
use Illuminate\Support\Carbon;

class Dashboard extends Component
{
    public int $periodDays = 30;

    // séries/labels do gráfico
    public array $movLabels = [];
    public array $movEntries = [];
    public array $movExits = [];

    // blocos inferiores
    public array $stockPerCategory = [];
    public array $topProductsByValue = [];

    // KPIs
    public float $totalStock = 0;
    public int $categoryCount = 0;
    public int $supplierCount = 0;
    public int $belowIdeal = 0;
    public array $produtosAbaixoIdeal = [];

    public function mount(?int $periodDays = null): void
    {
        $p = (int)($periodDays ?? request()->integer('period', 30));
        $this->periodDays = in_array($p, [7, 30, 180, 365], true) ? $p : 30;

        $this->buildMovementSeries();
        $this->buildKpisAndBlocks();
    }

    private function buildMovementSeries(): void
    {
        // labels diários (ex: 17/09)
        $start = now()->startOfDay()->subDays($this->periodDays - 1);
        $end   = now()->endOfDay();
        $days  = [];
        for ($d = 0; $d < $this->periodDays; $d++) {
            $day = $start->clone()->addDays($d);
            $key = $day->toDateString();     // 2025-09-17
            $days[$key] = $day->format('d/m');
        }
        $this->movLabels = array_values($days);

        // puxa todas as movimentações do período
        $moves = InventoryMovement::select('type', 'created_at')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($m) => Carbon::parse($m->created_at)->toDateString());

        $entries = [];
        $exits   = [];

        foreach ($days as $key => $label) {
            $group = $moves->get($key, collect());

            // Tipos de entrada e saída (compatível com valores antigos ‘in/out’)
            $entryTypes = [
                InventoryMovement::TYPE_ENTRY,
                InventoryMovement::TYPE_LOAN_RETURN,
                'in',
            ];
            $exitTypes = [
                InventoryMovement::TYPE_CONSUMPTION,
                InventoryMovement::TYPE_LOAN_OUT,
                InventoryMovement::TYPE_ADJUST, // trate ajustes como saída (neutro se qty negativa/positiva)
                'out',
            ];

            $entries[] = $group->whereIn('type', $entryTypes)->count();
            $exits[]   = $group->whereIn('type', $exitTypes)->count();
        }

        $this->movEntries = $entries;
        $this->movExits   = $exits;
    }

    private function productValue(Product $p): float
    {
        $price = (float)($p->avg_cost ?? $p->unit_price ?? 0);
        return round($p->available_count * $price, 2);
    }

    private function buildKpisAndBlocks(): void
    {
        $products = Product::with(['category', 'supplier'])->get();

        // KPIs
        $this->totalStock   = (float) $products->sum(fn($p) => $this->productValue($p));
        $this->categoryCount = Category::count();
        $this->supplierCount = Supplier::count();

        // Abaixo do ideal (não-consumíveis pela contagem de itens; consumíveis pelo snapshot de Inventory)
        $below = collect();

        // não-consumíveis
        $below = $below->merge(
            $products->filter(fn($p) => !$p->is_consumable && $p->available_count < (int)$p->min_stock)
                ->map(fn($p) => [
                    'name'    => $p->name,
                    'current' => (int)$p->available_count,
                    'ideal'   => (int)$p->min_stock,
                ])
        );

        // consumíveis (usa Inventory)
        $snapBelow = Inventory::with('product')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get()
            ->filter(fn($inv) => $inv->qnt_estoque < (int)$inv->qnt_ideal)
            ->map(fn($inv) => [
                'name'    => optional($inv->product)->name ?? '—',
                'current' => (int)$inv->qnt_estoque,
                'ideal'   => (int)$inv->qnt_ideal,
            ]);

        $this->produtosAbaixoIdeal = $below->merge($snapBelow)->values()->all();
        $this->belowIdeal = count($this->produtosAbaixoIdeal);

        // Estoque por categoria (R$)
        $this->stockPerCategory = Category::with('products')->get()
            ->map(function ($cat) {
                $value = (float) $cat->products->sum(fn($p) => $this->productValue($p));
                return ['name' => $cat->name, 'value' => round($value, 2)];
            })
            ->filter(fn($row) => $row['value'] > 0)
            ->values()
            ->all();

        // Top 5 por valor (R$)
        $this->topProductsByValue = $products
            ->map(fn($p) => ['name' => $p->name, 'value' => $this->productValue($p)])
            ->sortByDesc('value')
            ->take(5)
            ->values()
            ->all();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'periodDays'         => $this->periodDays,
            'movLabels'          => $this->movLabels,
            'movEntries'         => $this->movEntries,
            'movExits'           => $this->movExits,
            'stockPerCategory'   => $this->stockPerCategory,
            'topProductsByValue' => $this->topProductsByValue,
            'totalStock'         => $this->totalStock,
            'belowIdeal'         => $this->belowIdeal,
            'produtosAbaixoIdeal'=> $this->produtosAbaixoIdeal,
            'categoryCount'      => $this->categoryCount,
            'supplierCount'      => $this->supplierCount,
        ]);
    }
}
