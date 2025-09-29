<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserAdminController;
use App\Http\Controllers\Admin\LogAdminController;
use App\Http\Controllers\Admin\SettingsAdminController;
// (novo) imports para as rotas de e-mail/logs no admin
use App\Http\Controllers\Admin\EmailSettingsController;
use App\Http\Controllers\Admin\EmailLogController;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Teste Livewire (deixe público só se for intencional)
Route::get('/testelw', fn () => view('testelw'));

// ====== ROTAS AUTENTICADAS ======
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Produtos
    Route::resource('products', ProductController::class);
    Route::delete('products/{product}/remove-image', [ProductController::class, 'removeImage'])
        ->name('products.removeImage');

    // Itens do produto (parcial para accordion da listagem de estoque)
    Route::get('inventory/{product}/items/partial', [InventoryController::class, 'itemsPartial'])
        ->name('inventory.items.partial');

    // Categorias / Fornecedores / Usuários
    Route::resource('categories', CategoryController::class);
    Route::resource('suppliers',  SupplierController::class);
    Route::resource('users',      UserController::class)->except(['show']);

    // Estoque (snapshot/lista)
    Route::get('inventory/export', [InventoryController::class, 'exportCsv'])->name('inventory.export');
    Route::resource('inventory', InventoryController::class);

    // Itens do estoque (recebe {inventory})
    Route::get('inventory/{inventory}/items', [InventoryController::class, 'items'])
        ->name('inventory.items');

    // JSON de itens por produto (para accordion assíncrono)
    Route::get('inventory/{product}/items.json', [InventoryController::class, 'itemsJson'])
        ->name('inventory.items.json');

    // Movimentações
    Route::get('entries/create', [InventoryMovementController::class, 'createEntry'])->name('movements.createEntry');
    Route::post('movements/entry', [InventoryMovementController::class, 'storeEntry'])->name('movements.entry');
    Route::post('movements/exit',  [InventoryMovementController::class, 'storeExit'])->name('movements.exit');
    Route::get('movements',        [InventoryMovementController::class, 'index'])->name('movements.index');
    Route::get('movements/export', [InventoryMovementController::class, 'exportCsv'])->name('movements.export');
    Route::view('movements/history', 'movements.history')->name('movements.history');
    Route::get('/movements/timeline', [InventoryMovementController::class, 'timeline'])
        ->name('movements.timeline');

    // Ações por item
    Route::post('items/{item}/loan',    [InventoryMovementController::class, 'loanOut'])->name('inventory.loan.out');
    Route::post('items/{item}/return',  [InventoryMovementController::class, 'loanReturn'])->name('inventory.loan.return');
    Route::post('items/{item}/consume', [InventoryMovementController::class, 'consume'])->name('inventory.consume');
    Route::post('items/{item}/adjust',  [InventoryMovementController::class, 'adjust'])->name('inventory.adjust');

    // Perfil
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Auth
require __DIR__ . '/auth.php';

// ====== ADMIN ======
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('users', UserAdminController::class);
    Route::get('logs', [LogAdminController::class, 'index'])->name('logs.index');
    Route::get('logs/export', [LogAdminController::class, 'exportCsv'])->name('logs.export');
    Route::get('settings', [SettingsAdminController::class, 'index'])->name('settings');
    Route::get('settings/notifications', [SettingsAdminController::class, 'notifications'])->name('settings.notifications');
    Route::get('settings/backup', [SettingsAdminController::class, 'backup'])->name('settings.backup');
    Route::get('settings/permissions', [SettingsAdminController::class, 'permissions'])->name('settings.permissions');
});

// ====== ADMIN – E-mail (protegido) ======
Route::middleware(['auth', 'can:admin-actions'])
    ->prefix('admin/settings')
    ->group(function () {
        Route::get('email',  [EmailSettingsController::class, 'edit'])->name('admin.settings.email');
        Route::post('email', [EmailSettingsController::class, 'update'])->name('admin.settings.email.update');

        // (AJUSTE) teste de e-mail agora protegido e sob /admin/settings/email/test
        Route::post('email/test', [EmailSettingsController::class, 'test'])
            ->name('admin.settings.email.test');
    });

// ====== ADMIN – Logs de e-mail (protegido) ======
Route::middleware(['auth', 'can:admin-actions'])
    ->prefix('admin/logs')
    ->group(function () {
        Route::get('email',       [EmailLogController::class, 'index'])->name('admin.logs.email');
        Route::get('email/{id}',  [EmailLogController::class, 'show'])->name('admin.logs.email.show');
    });
