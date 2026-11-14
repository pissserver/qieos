<?php
    require_once __DIR__ . '/../script/session-bootstrap.php';
    $_SESSION = [];
    $cookieParams = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $cookieParams['path'],
        $cookieParams['domain'],
        $cookieParams['secure'],
        $cookieParams['httponly']
    );
    session_destroy();
    header("Location: ../sessions/sign-in.php?success=logout");
    exit();
