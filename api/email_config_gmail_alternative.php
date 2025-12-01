<?php
// ALTERNATIVE EMAIL CONFIG - Use Gmail SMTP for reliable delivery
// This is a quick test - switch back to Hostinger once you verify the account exists

return [
    // OPTION 1: Gmail SMTP (Most Reliable - Use if no-reply@deslink.id doesn't work)
    'smtp_host'   => 'smtp.gmail.com',
    'smtp_port'   => 587,
    'smtp_user'   => 'your-gmail@gmail.com',  // <- CHANGE THIS to your Gmail
    'smtp_pass'   => 'xxxx xxxx xxxx xxxx',   // <- CHANGE THIS to Gmail App Password (not regular password)
    'smtp_secure' => 'tls',

    // OPTION 2: Hostinger SMTP (Current - verify account exists first)
    // 'smtp_host'   => 'smtp.hostinger.com',
    // 'smtp_port'   => 465,
    // 'smtp_user'   => 'no-reply@deslink.id',
    // 'smtp_pass'   => '9vC8gYE&k&',
    // 'smtp_secure' => 'ssl',

    'from_email' => 'your-gmail@gmail.com',  // <- MUST match smtp_user for Gmail
    'from_name'  => 'RAB System',

    'roles' => [
        'pic' => [
            'from_email' => 'your-gmail@gmail.com',
            'from_name'  => 'RAB System - PIC',
            'force_to'   => [
                'arifa@dyandraeventsolutions.com',
                'irfant.giant@dyandraeventsolutions.com',
                'dellaazkia@dyandraeventsolutions.com',
                'admin@dyandraeventsolutions.com',
                'rajakautsar09@gmail.com'
            ]
        ],
        'gm' => [
            'from_email' => 'your-gmail@gmail.com',
            'from_name'  => 'RAB System - GM',
            'force_to'   => [
                'andysoekasah@dyandraeventsolutions.com',
                'tessya@dyandraeventsolutions.com',
                'bahri@dyandraeventsolutions.com',
                'admin@dyandraeventsolutions.com',
                'rajakautsar09@gmail.com'
            ]
        ],
        'admin' => [
            'from_email' => 'your-gmail@gmail.com',
            'from_name'  => 'RAB System - Admin',
            'force_to'   => [
                'mariatalia@dyandraeventsolutions.com'
            ]
        ],
    ],
];
?>
