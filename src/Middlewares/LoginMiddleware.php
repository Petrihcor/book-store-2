<?php

namespace App\Middlewares;

use Kernel\Session;

class LoginMiddleware
{
    public static function checkLogin()
    {
        #TODO избежать инстанс сессии
        $session = new Session();
        if (empty($session->getSession())) {
            header("Location: /login");
            exit;
        };
    }

    public static  function checkLogout()
    {
        #TODO избежать инстанс сессии
        $session = new Session();
        if (!empty($session->getSession())) {
            header("Location: /");
            exit;
        };
    }
}