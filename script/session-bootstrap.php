<?php
$sessionLifetime = 10 * 365 * 24 * 60 * 60;

ini_set('session.gc_maxlifetime', (string) $sessionLifetime);
$cookieParams = [
    'lifetime' => $sessionLifetime,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
];

if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params($cookieParams);
} else {
    session_set_cookie_params(
        $cookieParams['lifetime'],
        $cookieParams['path'],
        '',
        $cookieParams['secure'],
        $cookieParams['httponly']
    );
}

session_start();
