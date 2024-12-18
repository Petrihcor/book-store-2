<?php
use Symfony\Component\Routing\Route;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\RegistrationController;
use App\Controllers\LoginController;
use App\Controllers\CategoryController;

return [
    [
        "name" => "home",
        "route" => new Route('/', ['_controller' => [HomeController::class, 'index']])
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
    [
        "name" => "logout",
        "route" => new Route('/logout', ['_controller' => [LoginController::class, 'logout']])
    ],
    [
        "name" => "addController",
        "route" => new Route('/category/add', ['_controller' => [CategoryController::class, 'addCategory']], [], [], '', [], ['GET'])
    ],
    [
        "name" => "createController",
        "route" => new Route('/category/add', ['_controller' => [CategoryController::class, 'saveCategory']], [], [], '', [], ['POST'])
    ],
    [
        "name" => "showCategories",
        "route" => new Route('/categories', ['_controller' => [CategoryController::class, 'showCategories']])
    ],
];