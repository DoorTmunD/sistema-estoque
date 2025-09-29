<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\ProductItem;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        // ---- Filtro de período (7, 30, 180, 365) ----
        $periodDays = (int) $request->query('period', 30);
        if (! in_array($periodDays, [7, 30, 180, 365])) {
            $periodDays = 30;
        }

        // ---- KPIs base ----
        $categoryCount = Category::count();
        $supplierCount = Supplier::count();

        // Valor de estoque - CONSUMÍVEIS (Inventory x avg_cost)
        $consumableValue = Inventory::with('product:id,avg_cost,is_consumable,category_id')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get()
            ->sum(fn($inv) => (float)($inv->qnt_estoque ?? 0) * (float)($inv->product->avg_cost ?? 0));

        // Valor de estoque - NÃO CONSUMÍVEIS (itens disponíveis x avg_cost)
        $nonConsumable = Product::where('is_consumable', false)
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', ProductItem::STATUS_AVAILABLE);
            }])
            ->get();

        $nonConsumableValue = $nonConsumable->sum(
            fn($p) => (int)$p->available_count * (float)($p->avg_cost ?? 0)
        );

        $totalStock = round($consumableValue + $nonConsumableValue, 2);

        // ---- Abaixo do ideal (lista unificada) ----
        // Consumíveis: Inventory (qnt_estoque < qnt_ideal)
        $belowConsumables = Inventory::with('product:id,name,is_consumable')
            ->whereColumn('qnt_estoque', '<', 'qnt_ideal')
            ->get()
            ->map(fn($inv) => [
                'name'    => optional($inv->product)->name,
                'current' => (int)($inv->qnt_estoque ?? 0),
                'ideal'   => (int)($inv->qnt_ideal ?? 0),
            ]);

        // Não-consumíveis: produtos com min_stock e disponíveis < min_stock
        $belowNonConsum = Product::where('is_consumable', false)
            ->where('min_stock', '>', 0)
            ->withCount(['items as available_count' => function ($q) {
                $q->where('status', ProductItem::STATUS_AVAILABLE);
            }])
            ->get()
            ->filter(fn($p) => (int)$p->available_count < (int)($p->min_stock ?? 0))
            ->map(fn($p) => [
                'name'    => $p->name,
                'current' => (int)$p->available_count,
                'ideal'   => (int)($p->min_stock ?? 0),
            ]);

        $produtosAbaixoIdeal = $belowConsumables->merge($belowNonConsum)->values()->all();
        $belowIdeal          = count($produtosAbaixoIdeal);

        // ---- Séries de movimentação (por dia) no período ----
        $start = Carbon::now()->subDays($periodDays - 1)->startOfDay();
        $end   = Carbon::now()->endOfDay();

        // alguns projetos usam 'in'/'out', outros usam constantes; cobri ambos
        $rows = InventoryMovement::selectRaw("
                DATE(created_at) as d,
                SUM(CASE WHEN type IN ('ENTRY','in') THEN qty ELSE 0 END) as entries,
                SUM(CASE WHEN type IN ('CONSUMPTION','out') THEN qty ELSE 0 END) as consumption,
                SUM(CASE WHEN type IN ('LOAN_OUT') THEN qty ELSE 0 END) as loan_out,
                SUM(CASE WHEN type IN ('LOAN_RETURN') THEN qty ELSE 0 END) as loan_return
            ")
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $movLabels  = [];
        $movEntries = [];
        $movExits   = [];

        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $key = $date->toDateString();
            $movLabels[] = $date->format('d/m');

            $r  = $rows->get($key);
            $e  = (int)($r->entries      ?? 0);
            $c  = (int)($r->consumption  ?? 0);
            $lo = (int)($r->loan_out     ?? 0);
            $lr = (int)($r->loan_return  ?? 0);

            $movEntries[] = $e;
            $movExits[]   = max(0, ($c + $lo - $lr));
        }

        // ---- Estoque por categoria (valor R$) ----
        // Consumíveis agrupados por categoria
        $consGrouped = Inventory::with('product.category')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get()
            ->groupBy(fn($inv) => optional(optional($inv->product)->category)->name ?? 'Sem categoria')
            ->map(fn($g) => $g->sum(fn($inv) => (float)($inv->qnt_estoque ?? 0) * (float)($inv->product->avg_cost ?? 0)));

        // Não-consumíveis agrupados por categoria
        $nonGrouped = Product::where('is_consumable', false)
            ->with(['category', 'items' => function ($q) {
                $q->where('status', ProductItem::STATUS_AVAILABLE)->select('id', 'product_id');
            }])
            ->get()
            ->groupBy(fn($p) => optional($p->category)->name ?? 'Sem categoria')
            ->map(fn($g) => $g->sum(fn($p) => (int)$p->items->count() * (float)($p->avg_cost ?? 0)));

        // soma consumível + não-consumível por categoria
        $byCat = $consGrouped->union($nonGrouped)->map(function ($v, $k) use ($consGrouped, $nonGrouped) {
            return (float)($consGrouped->get($k, 0)) + (float)($nonGrouped->get($k, 0));
        })->sortDesc()->take(8);

        $stockPerCategory = $byCat->map(function ($value, $name) {
            return ['name' => $name, 'value' => round($value, 2)];
        })->values()->all();

        // ---- Top 5 produtos por VALOR em estoque ----
        $tops = collect();

        // consumíveis
        Inventory::with('product:id,name,avg_cost,is_consumable')
            ->whereHas('product', fn($q) => $q->where('is_consumable', true))
            ->get()
            ->each(function ($inv) use ($tops) {
                $tops->push([
                    'product_id' => $inv->product->id,
                    'name'       => $inv->product->name,
                    'value'      => (float)($inv->qnt_estoque ?? 0) * (float)($inv->product->avg_cost ?? 0),
                ]);
            });

        // não-consumíveis
        $nonConsumable->each(function ($p) use ($tops) {
            $tops->push([
                'product_id' => $p->id,
                'name'       => $p->name,
                'value'      => (int)$p->available_count * (float)($p->avg_cost ?? 0),
            ]);
        });

        $topProductsByValue = $tops
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

        // ---- Renderiza a página "dashboard-livewire" e repassa os dados para o componente Livewire ----
        return view('dashboard-livewire', [
            'periodDays'          => $periodDays,
            'totalStock'          => $totalStock,
            'categoryCount'       => $categoryCount,
            'supplierCount'       => $supplierCount,
            'belowIdeal'          => $belowIdeal,
            'produtosAbaixoIdeal' => $produtosAbaixoIdeal,

            'movLabels'           => $movLabels,
            'movEntries'          => $movEntries,
            'movExits'            => $movExits,

            'stockPerCategory'    => $stockPerCategory,
            'topProductsByValue'  => $topProductsByValue,
        ]);
    }
}
