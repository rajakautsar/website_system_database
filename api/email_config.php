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
            'force_to'   => [
                'Arifa (arifa@dyandraeventsolutions.com)',
                'Irfant (irfant.giant@dyandraeventsolutions.com)',
                'Della (dellaazkia@dyandraeventsolutions.com)',
                'Admin (admin@dyandraeventsolutions.com)',
            ]
        ],
        'gm' => [
            'from_email' => 'no-reply@deslink.id',
            'from_name'  => 'RAB System - GM',
            'force_to'   => [
                'Andy Soekasah (andysoekasah@dyandraeventsolutions.com)',
                'Tessya (tessya@dyandraeventsolutions.com)',
                'Bahri (bahri@dyandraeventsolutions.com)',
                'Admin (admin@dyandraeventsolutions.com)',
                

            ]
        ],
        'admin' => [
            'from_email' => 'no-reply@deslink.id',
            'from_name'  => 'RAB System - Admin Project',
            'force_to'   => [
                'mariatalia@dyandraeventsolutions.com'
            ]
        ],
    ],
];
