<?php

namespace App\Controllers;
use App\Post\Post;
use App\Post\PostService;
use Kernel\Controller\Controller;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\User\UserService;

class HomeController extends Controller
{
    public function index(): void
    {

        $postService = new PostService($this->getDatabase());
        $postsData = $postService->getPosts();
        $posts = [];
        foreach ($postsData as $postData) {
            $posts[] = new Post($postData['id'], $postData['name'], $postData['category_id'], $postData['user_id'], $postData['create_date'], $postData['update_date'], $postData['content']);
        }

        $data = [
            'title' => 'Welcome Page',
            'heading' => 'Hello, Twig!',
            'content' => 'This is a simple example of Twig integration.',
            'posts' => $posts
        ];

        echo $this->initTwig("pages/home", $data);


    }
}