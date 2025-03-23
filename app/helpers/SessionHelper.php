<?php
class SessionHelper
{
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public static function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header('Location: /T6-Sang/PROJECT1/Account/login');
            exit();
        }
    }

    public static function requireAdmin()
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            header('Location: /T6-Sang/PROJECT1/Product/list');
            exit();
        }
    }

    public static function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    public static function getUsername()
    {
        return $_SESSION['username'] ?? null;
    }

    public static function getRole()
    {
        return $_SESSION['role'] ?? null;
    }
}
