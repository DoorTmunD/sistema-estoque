<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\Inventory;
use App\Models\User;
use App\Models\InventoryMovement;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index()
    {
        // Totais gerais
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $totalSuppliers  = Supplier::count();
        $totalUsers      = User::count();

        // Itens abaixo, iguais e acima do ideal
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
            ])
            ->backgroundColor(['#EF4444', '#FBBF24', '#10B981']);

        // Usuários por nível
        $userChart = new Chart();
        $userChart
            ->labels(['Super-admin', 'Adm', 'Common'])
            ->dataset('Usuários', 'bar', [
                User::where('nivel', 'super-admin')->count(),
                User::where('nivel', 'adm')->count(),
                User::where('nivel', 'common')->count(),
            ])
            ->backgroundColor(['#3B82F6', '#6366F1', '#10B981']);

        // Movimentações deste mês: entradas vs saídas
        $startOfMonth  = Carbon::now()->startOfMonth();
        $entriesCount  = InventoryMovement::where('type', 'in')
                             ->where('created_at', '>=', $startOfMonth)
                             ->count();
        $exitsCount    = InventoryMovement::where('type', 'out')
                             ->where('created_at', '>=', $startOfMonth)
                             ->count();

        $movementChart = new Chart();
        $movementChart
            ->labels(['Entradas', 'Saídas'])
            ->dataset('Movimentações', 'bar', [
                $entriesCount,
                $exitsCount,
            ])
            ->backgroundColor(['#10B981', '#EF4444']);

        return view('dashboard', compact(
            'totalProducts',
            'totalCategories',
            'totalSuppliers',
            'totalUsers',
            'lowStockChart',
            'userChart',
            'movementChart'
        ));
    }
}