<?php
return array(
    'isbank' => array(
        'adapter' => 'Est',
        'api_url' => "https://testsanalpos.est.com.tr/servlet/cc5ApiServer",
        'client_id' => '700100000',
        'username' => "ISBANKAPI",
        'password' => "ISBANK07",
        'mode' => 'T',
        'testcard' => array(
            'number' => "4508034508034509",
            'cvv' => "000",
            'expire_month' => "12",
            'expire_year' => "15",
        ),
    ),
    'posnet_bank' => array(
        'adapter' => 'Gvp',
        'api_url' => "https://sanalposprovtest.garanti.com.tr/VPServlet",
        'terminal_id' => "30691242",
        'merchant_id' => "7000679",
        'auth_username' => 'PROVAUT',
        'auth_password' => '123qweASD',
        'refund_username' => 'PROVRFN',
        'refund_password' => '123qweASD',
        'mode' => 'TEST',
        'testcard' => array(
            'number' => "5407099016729014",
            'cvv' => "395",
            'expire_month' => "03",
            'expire_year' => "14",
        ),
    ),
);
