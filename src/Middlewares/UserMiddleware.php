<?php

namespace App\Middlewares;

use Kernel\Session;

class UserMiddleware
{
    public function checkUser()
    {
        $session = new Session();
        dd($session->getSession());
    }
}