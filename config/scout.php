<?php

return [

    // Driver padrão de busca (usando Algolia)
    'driver' => env('SCOUT_DRIVER', 'algolia'),

    // Prefixo dos índices (caso use multi-tenancy)
    'prefix' => env('SCOUT_PREFIX', ''),

    // As operações de sync (indexação) vão ser enfileiradas? (para grandes volumes, true melhora performance)
    'queue' => env('SCOUT_QUEUE', false),

    // Só sincroniza depois de transações concluídas?
    'after_commit' => false,

    // Tamanhos dos chunks para importação em massa
    'chunk' => [
        'searchable'   => 500,
        'unsearchable' => 500,
    ],

    // Soft delete mantém registros excluídos nos índices?
    'soft_delete' => false,

    // Identificar usuário nas buscas (Algolia suporta analytics por usuário, mas não obrigatório)
    'identify' => env('SCOUT_IDENTIFY', false),

    // Configuração Algolia
    'algolia' => [
        'id'     => env('ALGOLIA_APP_ID', ''),
        'secret' => env('ALGOLIA_SECRET', ''),
        // Adicione configurações de índices customizados aqui se quiser, ex:
        // 'index-settings' => [
        //     'products' => ['searchableAttributes' => ['name', 'description']],
        // ],
    ],

];