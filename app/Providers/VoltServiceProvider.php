<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

/**
 * Provider responsável por montar os diretórios das views Livewire Volt.
 * Permite organização escalável dos componentes e páginas, mantendo o padrão SaaS.
 */
class VoltServiceProvider extends ServiceProvider
{
    /**
     * Register serviços e bindings customizados.
     */
    public function register(): void
    {
        // Caso precise registrar singletons, bindings, helpers de Volt, adicione aqui.
    }

    /**
     * Bootstrap dos serviços Volt.
     * Aqui define os diretórios de views que o Volt irá montar automaticamente.
     */
    public function boot(): void
    {
        Volt::mount([
            config('livewire.view_path', resource_path('views/livewire')),
            resource_path('views/pages'),
            // Adicione outros paths conforme crescer o projeto (ex: módulos, plugins)
            // resource_path('views/admin'),
        ]);
    }
}