<?php

$dbPath = $_ENV['DB_DATABASE'] ?? __DIR__ . '/../storage/database.sqlite';

if (! str_starts_with($dbPath, '/') && ! str_contains($dbPath, ':\\')) {
    $dbPath = dirname(__DIR__) . '/' . ltrim($dbPath, '/');
}

$dbDir = dirname($dbPath);
if (! is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

if (! file_exists($dbPath) && str_ends_with($dbPath, '.sqlite')) {
    touch($dbPath);
}

return [
    'default' => $_ENV['DB_CONNECTION'] ?? 'sqlite',

    'connections' => [
        'sqlite' => [
            'driver' => 'sqlite',
            'database' => $dbPath,
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
        'mysql' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? '3306',
            'database' => $_ENV['DB_DATABASE'] ?? 'laralite',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ],
    ],
];
