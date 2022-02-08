<?php
    require_once(__DIR__ . '/../src/init.php');

    use app\users\User;

    if(!User::isLoggedIn()) {
        header('Location: ' . ROOT_PATH);
    }

    session_destroy();
    header('Location: ' . ROOT_PATH . 'login');