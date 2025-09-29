<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * Provider responsável por todas as políticas de autorização da aplicação.
 * Inclui policies por model e gates globais para níveis e features do sistema.
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping de models para suas policies.
     * Cada policy é responsável por gerenciar as permissões daquele model.
     */
    protected $policies = [
        \App\Models\Product::class => \App\Policies\ProductPolicy::class, // Permissões de produtos
        \App\Models\Category::class => \App\Policies\CategoryPolicy::class, // Permissões de categorias
        \App\Models\Supplier::class => \App\Policies\SupplierPolicy::class, // Permissões de fornecedores
        \App\Models\User::class => \App\Policies\UserPolicy::class, // Permissões de usuários
        \App\Models\Inventory::class => \App\Policies\InventoryPolicy::class, // Permissões de inventário
        \App\Models\InventoryMovement::class => \App\Policies\InventoryMovementPolicy::class, // Permissões de movimentações
    ];

    /**
     * Bootstrap das políticas e gates globais.
     * Inclui regras para super-admin, admin, operador e visualização.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // GATE GLOBAL: Super-admin faz tudo, acima das policies
        Gate::before(function ($user, $ability) {
            return $user->nivel === 'super-admin' ? true : null;
        });

        // Gate para administradores (admin e super-admin)
        Gate::define('admin-actions', function ($user) {
            return in_array($user->nivel, ['super-admin', 'adm']);
        });

        // Gate para operadores (admin, super-admin e operador)
        Gate::define('operator-actions', function ($user) {
            return in_array($user->nivel, ['super-admin', 'adm', 'operador']);
        });

        // Gate para qualquer usuário autenticado
        Gate::define('visualizar', function ($user) {
            return in_array($user->nivel, ['super-admin', 'adm', 'operador', 'common']);
        });

        // Exemplos para expansão futura:
        // Gate::define('export-relatorios', function ($user) {
        //     return in_array($user->nivel, ['super-admin', 'adm']);
        // });
        // Gate::define('acessar-auditoria', function ($user) {
        //     return $user->nivel === 'super-admin';
        // });
    }
}