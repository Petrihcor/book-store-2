<?php

namespace Kernel;

class Session
{

    public function __construct()
    {
        session_start();
    }

    public function setSession(string $key, $value)
    {
        return $_SESSION[$key] = $value;
    }

    public function getSession()
    {
        return $_SESSION;
    }

    public function destroySession()
    {
        session_destroy();
    }
    
}