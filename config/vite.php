<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base URL para asset()
    |--------------------------------------------------------------------------
    */
    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Caminho do Manifest gerado pelo Vite
    |--------------------------------------------------------------------------
    | O Vite 6 grava em public/build/.vite/manifest.json por padrão.
    */
    'manifest' => env('VITE_MANIFEST_PATH', 'public/build/.vite/manifest.json'),

    /*
    |--------------------------------------------------------------------------
    | HMR (npm run dev)
    |--------------------------------------------------------------------------
    */
    'hot_module_replacement' => [
        'host' => env('VITE_HMR_HOST', 'localhost'),
        'port' => env('VITE_HMR_PORT', 5173),
    ],
];
