<?php

namespace Kernel;

use Kernel\Routs\Router;

class App
{
    public function run()
    {
        $routs = new Router();
        $routs->dispatch();
    }
}