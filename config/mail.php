<?php

return [
    'default' => $_ENV['MAIL_MAILER'] ?? 'smtp',

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => $_ENV['MAIL_HOST'] ?? '127.0.0.1',
            'port' => (int) ($_ENV['MAIL_PORT'] ?? 1025),
            'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? null,
            'username' => $_ENV['MAIL_USERNAME'] ?? null,
            'password' => $_ENV['MAIL_PASSWORD'] ?? null,
            'timeout' => null,
        ],
        'array' => [
            'transport' => 'array',
        ],
    ],

    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'hello@laralite.dev',
        'name' => $_ENV['MAIL_FROM_NAME'] ?? 'Laralite App',
    ],
];
