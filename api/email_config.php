<?php

return [
    // Default SMTP configuration (Hostinger)
    'smtp_host'   => 'smtp.hostinger.com',
    'smtp_port'   => 465,
    'smtp_user'   => 'no-reply@deslink.id',
    'smtp_pass'   => '9vC8gYE&k&',
    'smtp_secure' => 'ssl',

    // Default sender identity
    'from_email' => 'no-reply@deslink.id',
    'from_name'  => 'RAB System',

    // Role-based overrides
    'roles' => [

        // PIC role → selalu kirim ke email pribadi kamu
        'pic' => [
            'from_email' => 'no-reply@deslink.id',
            'from_name'  => 'RAB System - PIC',
            'force_to'   => ['rajakautsar09@gmail.com']
        ],

        // GM role → email lain (misalnya email testing)
        'gm' => [
            'from_email' => 'no-reply@deslink.id',
            'from_name'  => 'RAB System - GM',
            'force_to'   => ['rajakautsar119@gmail.com']
        ],
    ],
];
