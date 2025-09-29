<?php

return [

    // Usa múltiplos times? (normalmente false)
    'teams' => false,

    // Nomes das tabelas usadas pelo pacote
    'table_names' => [
        'roles'                 => 'roles',
        'permissions'           => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles'       => 'model_has_roles',
        'role_has_permissions'  => 'role_has_permissions',
    ],

    // Nomes de colunas usadas nas tabelas/pivots
    'column_names' => [
        // chave do morph (users, etc.)
        'model_morph_key'     => 'model_id',
        // se usar teams=true
        'team_foreign_key'    => 'team_id',
        // chaves dos pivots
        'role_pivot_key'       => 'role_id',
        'permission_pivot_key' => 'permission_id',
    ],

    // Exibir permissões/roles nas Exceptions (debug)
    'display_permission_in_exception' => false,
    'display_role_in_exception'       => false,

    // Cache
    'cache' => [
        'expiration_time' => \DateInterval::createFromDateString('24 hours'),
        'key'             => 'spatie.permission.cache',
        'model_key'       => 'name',
        'identifier'      => 'id',
        'store'           => 'default',
    ],
];
