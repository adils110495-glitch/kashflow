<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    //'bsDependencyEnabled' => false, 
    'bsVersion' => '5.x',
    
    // Mailer configuration
    'mailer' => [
        'host' => 'mail.leanport.info',
        'username' => 'adil.saifi@leanport.info',
        'password' => 'Mainuddin@123',
        'port' => 587,
        //'dsn' => 'smtp://your-email@gmail.com:your-app-password@smtp.gmail.com:587',
        'encryption' => 'tls',
        'from' => [
            'noreply@example.com' => 'Kashflow',
        ],
    ],
];
