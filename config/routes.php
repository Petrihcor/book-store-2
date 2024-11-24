<?php
use Symfony\Component\Routing\Route;
use App\Controllers\HomeController;
use App\Controllers\AboutController;

return [
    [
        "name" => "home",
        "route" => new Route('/home', ['_controller' => [HomeController::class, 'index']])
    ],
    [
        "name" => "about",
        "route" => new Route('/about', ['_controller' => [AboutController::class, 'index']])
    ],
];