<?php
return [
    'default' => 'rangine',
    'sandbox' => env('SMS_SANDBOX', true),
    'gateway' => [
        'melipayamak' => [
            'website'    => 'http://melipayamak.ir',
            'webService' => 'http://melipayamak.ir/post/send.asmx?wsdl',
            'username'   => '',
            'password'   => '',
            'from'       => '',
        ],
        'rangine'     => [
            'website'    => 'https://sms.rangine.ir/',
            'webService' => 'https://api2.ippanel.com/api/v1',
            'apiKey'     => env('SMS_API_KEY', ''),
            'from'       => env('SMS_FROM', '5000 '),
        ],
    ],

];
