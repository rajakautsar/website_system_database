<?php

return [
    'smtp_host'   => 'smtp.hostinger.com',
    'smtp_port'   => 465,
    'smtp_user'   => 'no-reply@deslink.id',
    'smtp_pass'   => '9vC8gYE&k&',
    'smtp_secure' => 'ssl',

    'from_email' => 'no-reply@deslink.id',
    'from_name'  => 'RAB System',

    'roles' => [

        'pic' => [
            'from_email' => 'no-reply@deslink.id',
            'from_name'  => 'RAB System - PIC',
            'force_to'   => ['rajakautsar09@gmail.com']
        ],
        'gm' => [
            'from_email' => 'no-reply@deslink.id',
            'from_name'  => 'RAB System - GM',
            'force_to'   => ['rajakautsar119@gmail.com']
        ],
    ],
];
