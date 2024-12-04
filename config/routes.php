<?php
use Symfony\Component\Routing\Route;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\RegistrationController;

return [
    [
        "name" => "home",
        "route" => new Route('/home', ['_controller' => [HomeController::class, 'index']])
    ],
    [
        "name" => "about",
        "route" => new Route('/about', ['_controller' => [AboutController::class, 'index']])
    ],
    [
        "name" => "registration",
        "route" => new Route('/registration', ['_controller' => [RegistrationController::class, 'register']])
    ],
    [
        "name" => "saveUser",
        "route" => new Route('/save-user', ['_controller' => [RegistrationController::class, 'saveUser']], [], [], '', [], ['POST'])
    ],
];