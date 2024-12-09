<?php
use Symfony\Component\Routing\Route;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\RegistrationController;
use App\Controllers\LoginController;

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
        "route" => new Route('/registration', ['_controller' => [RegistrationController::class, 'register']], [], [], '', [], ['GET'])
    ],
    [
        "name" => "saveUser",
        "route" => new Route('/registration', ['_controller' => [RegistrationController::class, 'saveUser']], [], [], '', [], ['POST'])
    ],
    [
        "name" => "login",
        "route" => new Route('/login', ['_controller' => [LoginController::class, 'index']], [], [], '', [], ['GET'])
    ],
    [
        "name" => "setUser",
        "route" => new Route('/login', ['_controller' => [LoginController::class, 'login']], [], [], '', [], ['POST'])
    ],
];