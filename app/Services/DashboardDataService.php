<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardDataService
{
    /**
     * KPIs e listas base para o dashboard.
     * $days: 7 | 30 | 180 | 365
     */
    public function summary(int $days = 30): array
    {
        $days = in_array($days, [7, 30, 180, 365]) ? $days : 30;

        // Valor de estoque de CONSUMÍVEIS (usa Inventory)
        $consumableValue = Inventory::with('product:id,avg_cost,is_consumable')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get()
            ->sum(fn($inv) => (float)($inv->qnt_estoque ?? 0) * (float)($inv->product->avg_cost ?? 0));

        // Valor de estoque de NÃO-CONSUMÍVEIS (itens disponíveis * custo médio)
        $nonConsumable = Product::where('is_consumable', false)
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', ProductItem::STATUS_AVAILABLE);
            }])
            ->get();

        $nonConsumableValue = $nonConsumable->sum(
            fn($p) => (int)$p->available_count * (float)($p->avg_cost ?? 0)
        );

        $totalStockValue = round($consumableValue + $nonConsumableValue, 2);

        // Abaixo do ideal (apenas o que tem linha no Inventory)
        $belowIdeal = Inventory::whereColumn('qnt_estoque', '<', 'qnt_ideal')->count();

        // Contadores simples
        $categories = Category::count();
        $suppliers  = Supplier::count();

        // Top produtos por valor em estoque (mix de consumíveis e não-consumíveis)
        $tops = $this->topProductsByValue();

        // Série de movimentação (entrada vs saídas/empréstimos/devoluções) no período
        $movSeries = $this->movementsNetPerDay($days);

        // Estoque por categoria (R$)
        $byCategory = $this->stockValueByCategory();

        return [
            'totals' => [
                'stock_value'  => $totalStockValue,
                'below_ideal'  => $belowIdeal,
                'categories'   => $categories,
                'suppliers'    => $suppliers,
            ],
            'tops'         => $tops,
            'mov_series'   => $movSeries,
            'by_category'  => $byCategory,
            'period_days'  => $days,
        ];
    }

    /** Retorna série diária com entradas (+) e saídas líquidas (–). */
    public function movementsNetPerDay(int $days = 30): array
    {
        $start = Carbon::now()->subDays($days - 1)->startOfDay();
        $end   = Carbon::now()->endOfDay();

        $rows = InventoryMovement::selectRaw("
                DATE(created_at) as d,
                SUM(CASE WHEN type = ? THEN qty ELSE 0 END) as entries,
                SUM(CASE WHEN type = ? THEN qty ELSE 0 END) as loan_out,
                SUM(CASE WHEN type = ? THEN qty ELSE 0 END) as loan_return,
                SUM(CASE WHEN type = ? THEN qty ELSE 0 END) as consumption
            ", [
                InventoryMovement::TYPE_ENTRY,
                InventoryMovement::TYPE_LOAN_OUT,
                InventoryMovement::TYPE_LOAN_RETURN,
                InventoryMovement::TYPE_CONSUMPTION,
            ])
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $labels = [];
        $entries = [];
        $exits   = []; // consumo + empréstimo - devolução (resultado negativo)

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $key = $date->toDateString();
            $labels[] = $date->format('d/m');

            $r = $rows->get($key);
            $e = (int)($r->entries ?? 0);
            $c = (int)($r->consumption ?? 0);
            $lo = (int)($r->loan_out ?? 0);
            $lr = (int)($r->loan_return ?? 0);

            $entries[] = $e;
            $exits[]   = max(0, ($c + $lo - $lr)); // não deixa negativo na série
        }

        return [
            'labels'  => $labels,
            'entries' => $entries,
            'exits'   => $exits,
        ];
    }

    /** Valor em estoque por categoria (R$) */
    public function stockValueByCategory(): array
    {
        // Consumíveis (Inventory * avg_cost) agrupado por categoria
        $consumables = Inventory::with('product.category')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get()
            ->groupBy(fn($inv) => optional($inv->product->category)->name ?? 'Sem categoria')
            ->map(fn(Collection $group) => $group->sum(function ($inv) {
                return (float)($inv->qnt_estoque ?? 0) * (float)($inv->product->avg_cost ?? 0);
            }));

        // Não-consumíveis (itens disponiveis * avg_cost) agrupado por categoria
        $non = Product::where('is_consumable', false)
            ->with(['category', 'items' => function ($q) {
                $q->where('status', ProductItem::STATUS_AVAILABLE)->select('id', 'product_id');
            }])
            ->get()
            ->groupBy(fn($p) => optional($p->category)->name ?? 'Sem categoria')
            ->map(fn(Collection $group) => $group->sum(function ($p) {
                return (int)($p->items->count()) * (float)($p->avg_cost ?? 0);
            }));

        // Soma e ordena
        $all = $consumables->union($non)->map(function ($v, $k) use ($consumables, $non) {
            return (float)($consumables->get($k, 0)) + (float)($non->get($k, 0));
        });

        // pega as top 8 categorias
        $sorted = $all->sortDesc()->take(8);

        return [
            'labels' => $sorted->keys()->values()->all(),
            'values' => $sorted->values()->all(),
        ];
    }

    /** Top 5 produtos por valor em estoque (R$) */
    public function topProductsByValue(): array
    {
        $list = collect();

        // Consumíveis
        $cons = Inventory::with('product:id,name,avg_cost,is_consumable')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get();

        foreach ($cons as $inv) {
            $list->push([
                'product_id' => $inv->product->id,
                'name'       => $inv->product->name,
                'value'      => (float)($inv->qnt_estoque ?? 0) * (float)($inv->product->avg_cost ?? 0),
            ]);
        }

        // Não-consumíveis
        $non = Product::where('is_consumable', false)
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', ProductItem::STATUS_AVAILABLE);
            }])
            ->get();

        foreach ($non as $p) {
            $list->push([
                'product_id' => $p->id,
                'name'       => $p->name,
                'value'      => (int)$p->available_count * (float)($p->avg_cost ?? 0),
            ]);
        }

        return $list
            ->groupBy('product_id')
            ->map(fn($g) => [
                'product_id' => $g->first()['product_id'],
                'name'       => $g->first()['name'],
                'value'      => round($g->sum('value'), 2),
            ])
            ->sortByDesc('value')
            ->values()
            ->take(5)
            ->all();
    }
}
