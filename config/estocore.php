<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Feature Toggles & Custom EstoCore Settings
    |--------------------------------------------------------------------------
    |
    | Centralize aqui todas as configurações customizadas do EstoCore
    | para facilitar ativação/desativação de features e ajustes de produto.
    | Use ENV para que seja fácil trocar entre ambientes (prod, dev, etc).
    |
    */

    // Ativa/desativa a busca global (Algolia)
    'global_search_enabled' => env('ESTOCORE_GLOBAL_SEARCH_ENABLED', true),

    // Ativa modo demo, exibe faixas de aviso e limita funcionalidades
    'demo_mode' => env('DEMO_MODE', false),

    // Exemplo de futuro toggle: relatórios exportáveis (CSV/PDF)
    'reports_enabled' => env('ESTOCORE_REPORTS_ENABLED', true),

    // Ativa/desativa quick actions no FAB
    'quick_actions_enabled' => env('ESTOCORE_QUICK_ACTIONS_ENABLED', true),

    // Limite máximo de upload por arquivo (em MB, exemplo)
    'max_upload_mb' => env('ESTOCORE_MAX_UPLOAD_MB', 10),

    // Customização visual: cores do tema
    'theme_color' => env('ESTOCORE_THEME_COLOR', '#23F6F8'),

    // Nome da empresa/cliente para personalizar branding
    'customer_brand' => env('ESTOCORE_CUSTOMER_BRAND', 'EstoCore'),

    
];