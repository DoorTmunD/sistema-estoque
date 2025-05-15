<?php

namespace App\Livewire;

use Livewire\Component;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        // 1) Estoque vs Ideal
        $counts = Inventory::selectRaw("
            SUM(CASE WHEN qnt_estoque < qnt_ideal THEN 1 ELSE 0 END) as low,
            SUM(CASE WHEN qnt_estoque = qnt_ideal THEN 1 ELSE 0 END) as equal,
            SUM(CASE WHEN qnt_estoque > qnt_ideal THEN 1 ELSE 0 END) as high
        ")->first();

        $lowStockChart = new Chart();
        $lowStockChart
             ->labels(['Abaixo do Ideal', 'No Ideal', 'Acima do Ideal'])
             ->dataset('Estoque', 'doughnut', [
                 $counts->low,
                 $counts->equal,
                 $counts->high,
             ]);

        // 2) Usuários por Nível
        $userChart = new Chart();
        $userChart
             ->labels(['Super-admin', 'Adm', 'Common'])
             ->dataset('Usuários', 'bar', [
                 User::where('nivel', 'super-admin')->count(),
                 User::where('nivel', 'adm')->count(),
                 User::where('nivel', 'common')->count(),
             ]);

        // 3) Movimentações do mês
        $startOfMonth = Carbon::now()->startOfMonth();
        $entriesCount = InventoryMovement::where('type', 'in')
                            ->where('created_at', '>=', $startOfMonth)
                            ->count();
        $exitsCount   = InventoryMovement::where('type', 'out')
                            ->where('created_at', '>=', $startOfMonth)
                            ->count();

        $movementChart = new Chart();
        $movementChart
             ->labels(['Entradas', 'Saídas'])
             ->dataset('Movimentações', 'bar', [
                 $entriesCount,
                 $exitsCount,
             ]);

        // 4) Saídas últimos 6 meses
        $months = collect(range(5, 0, -1))
            ->map(fn($i) => now()->subMonths($i)->format('M'));

        $salesData = $months->map(function($label, $idx) {
            $monthNum = now()->subMonths(5 - $idx)->month;
            return InventoryMovement::where('type', 'out')
                        ->whereMonth('created_at', $monthNum)
                        ->count();
        });

        $salesChart = new Chart();
        $salesChart
             ->labels($months->toArray())
             ->dataset('Saídas (vendas)', 'bar', $salesData->toArray())
             ->options([
                 'responsive' => true,
                 'legend'     => ['position' => 'bottom'],
             ]);

        return view('livewire.dashboard', [
            'lowStockChart' => $lowStockChart,
            'userChart'     => $userChart,
            'movementChart' => $movementChart,
            'salesChart'    => $salesChart,
            'totalProducts'   => Product::count(),
            'totalCategories' => Category::count(),
            'totalSuppliers'  => Supplier::count(),
            'totalUsers'      => User::count(),
        ]);
    }
}