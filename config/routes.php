<?php
use Symfony\Component\Routing\Route;
use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\RegistrationController;
use App\Controllers\LoginController;
use App\Controllers\CategoryController;
use App\Controllers\PostController;

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
        "name" => "category_add",
        "route" => new Route('/category/add', ['_controller' => [CategoryController::class, 'addCategory']], [], [], '', [], ['GET'])
    ],
    [
        "name" => "category_create",
        "route" => new Route('/category/add', ['_controller' => [CategoryController::class, 'saveCategory']], [], [], '', [], ['POST'])
    ],
    [
        "name" => "showCategories",
        "route" => new Route('/categories', ['_controller' => [CategoryController::class, 'showCategories']])
    ],
    [
        "name" => "category_page",
        "route" => new Route('/category/{id}', ['_controller' => [CategoryController::class, 'catPage']])
    ],
    [
        "name" => "category_edit",
        "route" => new Route('/category/edit/{id}', ['_controller' => [CategoryController::class, 'editCategory']])
    ],
    [
        "name" => "category_update",
        "route" => new Route('/update/category', ['_controller' => [CategoryController::class, 'updateCategory']], [], [], '', [], ['POST'])
    ],
    [
        "name" => "category_delete",
        "route" => new Route('/category/delete/{id}', ['_controller' => [CategoryController::class, 'deleteCategory']])
    ],
    [
        "name" => "post_add",
        "route" => new Route('/post/add', ['_controller' => [PostController::class, 'addPost']], [], [], '', [], ['GET'])
    ],
    [
        "name" => "post_create",
        "route" => new Route('/post/add', ['_controller' => [PostController::class, 'savePost']], [], [], '', [], ['POST'])
    ],
    [
        "name" => "post_page",
        "route" => new Route('/post/{id}', ['_controller' => [PostController::class, 'index']])
    ],

];