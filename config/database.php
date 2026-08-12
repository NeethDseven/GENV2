<?php

declare(strict_types=1);

/**
 * Persistence GENESIS.
 * - Défaut : SQLite (storage/genesis.sqlite) — aucun serveur requis.
 * - Option : MySQL XAMPP (définir GENESIS_DB_DRIVER=mysql + démarrer MySQL).
 */
return [
    // 'sqlite' | 'mysql' | 'auto' (tente mysql puis sqlite)
    'driver' => getenv('GENESIS_DB_DRIVER') ?: 'auto',

    'sqlite' => [
        'path' => dirname(__DIR__) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'genesis.sqlite',
    ],

    'mysql' => [
        'host' => getenv('GENESIS_DB_HOST') ?: '127.0.0.1',
        'port' => (int) (getenv('GENESIS_DB_PORT') ?: 3306),
        'database' => getenv('GENESIS_DB_NAME') ?: 'genesis',
        'user' => getenv('GENESIS_DB_USER') ?: 'root',
        'password' => getenv('GENESIS_DB_PASS') !== false ? (string) getenv('GENESIS_DB_PASS') : '',
        'charset' => 'utf8mb4',
    ],
];
