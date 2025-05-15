<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryMovementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redireciona raiz para login ou dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Rotas que exigem autenticação
Route::middleware(['auth'])->group(function () {

    // Dashboard (invoca o componente Livewire)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })
    ->middleware('verified')
    ->name('dashboard');

    // CRUD de Produtos, Categorias e Fornecedores
    Route::resource('products', ProductController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers', SupplierController::class);

    // Gestão de Usuários (apenas super-admin)
    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Exportar estoque para CSV
    Route::get('inventory/export', [InventoryController::class, 'exportCsv'])
         ->name('inventory.export');

    // Estoque e Movimentações (entrada/saída)
    Route::middleware('can:manage-products')->group(function () {

        // CRUD de inventário
        Route::resource('inventory', InventoryController::class);

        // Formulário de entrada de estoque
        Route::get('entries/create', [InventoryMovementController::class, 'createEntry'])
             ->name('movements.createEntry');

        // Registrar entrada de estoque
        Route::post('movements/entry', [InventoryMovementController::class, 'storeEntry'])
             ->name('movements.entry');

        // Registrar saída de estoque
        Route::post('movements/exit', [InventoryMovementController::class, 'storeExit'])
             ->name('movements.exit');

        // Exportar histórico de movimentações para CSV
        Route::get('movements/export', [InventoryMovementController::class, 'exportCsv'])
             ->name('movements.export');

        // Página de histórico de movimentações
        Route::get('movements/history', function () {
            return view('movements.history');
        })
        ->name('movements.history');
    });

    // Perfil do usuário
    Route::get('profile', [ProfileController::class, 'edit'])
         ->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])
         ->name('profile.update');
});

// Rotas de autenticação (login, registro, logout, etc.)
require __DIR__ . '/auth.php';