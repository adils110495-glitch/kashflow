<?php

return [
    'adminEmail' => 'admin@kashflow.com',
    'senderEmail' => 'noreply@kashflow.com',
    'senderName' => 'KashFlow System',
    //'bsDependencyEnabled' => false, 
    'bsVersion' => '5.x',
    
    // Mailer configuration
    'mailer' => [
        'host' => getenv('MAILER_HOST') ?: 'smtp.gmail.com',
        'username' => getenv('MAILER_USERNAME') ?: 'your-email@gmail.com',
        'password' => getenv('MAILER_PASSWORD') ?: 'your-app-password',
        'port' => getenv('MAILER_PORT') ?: '587',
        'encryption' => getenv('MAILER_ENCRYPTION') ?: 'tls',
        'from' => [
            (getenv('MAILER_FROM_EMAIL') ?: 'noreply@kashflow.com') => (getenv('MAILER_FROM_NAME') ?: 'KashFlow System'),
        ],
    ],
];
