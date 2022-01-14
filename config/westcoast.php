<?php

return [
    'loginId'       => env('WESTCOAST_LOGIN_ID'),
    'password'      => env('WESTCOAST_PASSWORD'),
    'company'       => env('WESTCOAST_COMPANY'),
    'auth_username' => env('WESTCOAST_AUTH_USERNAME'),
    'auth_password' => env('WESTCOAST_AUTH_PASSWORD'),
    'timeout'       => env('WESTCOAST_TIMEOUT', 30),
    'debug'         => env('WESTCOAST_DEBUG', 0),
];
