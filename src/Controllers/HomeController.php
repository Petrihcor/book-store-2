<?php

namespace App\Controllers;
use Kernel\Controller\Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\User\UserService;

class HomeController extends Controller
{
    public function index(): void
    {

        $data = [
            'title' => 'Welcome Page',
            'heading' => 'Hello, Twig!',
            'content' => 'This is a simple example of Twig integration.',
        ];
        $users = new UserService($this->getDatabase());
        echo $this->initTwig("pages/home", $data);


    }
}