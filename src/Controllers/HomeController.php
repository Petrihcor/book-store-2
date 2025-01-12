<?php

namespace App\Controllers;
use App\Post\Post;
use App\Post\PostService;
use Kernel\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use App\User\UserService;

class HomeController extends Controller
{
    public function index(Request $request): void
    {

        $postService = new PostService($this->getDatabase());
        $page = (int)($request->query->get('page', 1)); // Текущая страница
        $itemsPerPage = 4; // Количество элементов на странице

        $postsData = $postService->getPosts($page, $itemsPerPage);
        $posts = [];

        foreach ($postsData['posts'] as $postData) {
            $posts[] = new Post($postData['id'], $postData['name'], $postData['category_id'], $postData['user_id'], $postData['create_date'], $postData['update_date'], $postData['content']);
        }
        $totalPosts = $postsData['total'];
        $totalPages = (int)ceil($totalPosts / $itemsPerPage);

        $data = [
            'title' => 'Welcome Page',
            'heading' => 'Hello, Twig!',
            'content' => 'This is a simple example of Twig integration.',
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ];

        echo $this->initTwig("pages/home", $data);


    }
}