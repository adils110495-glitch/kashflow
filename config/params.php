<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    //'bsDependencyEnabled' => false, 
    'bsVersion' => '5.x',
    
    // Mailer configuration
    'mailer' => [
        'host' => getenv('MAILER_HOST'),
        'username' => getenv('MAILER_USERNAME'),
        'password' => getenv('MAILER_PASSWORD'),
        'port' => getenv('MAILER_PORT'),
        'encryption' => getenv('MAILER_ENCRYPTION'),
        'from' => [
            getenv('MAILER_FROM_EMAIL') => getenv('MAILER_FROM_NAME'),
        ],
    ],
];
