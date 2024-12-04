<?php

namespace Kernel;

use Kernel\Routs\Router;
use Kernel\Database\Database;
use Doctrine\DBAL\DriverManager;

class App
{
    public function run()
    {
        $config = require __DIR__ . "/../config/db.php";
        $db = new Database($config);
        $db->connect();
        $routes = new Router("config/routes.php");
        $routes->dispatch();
    }
}