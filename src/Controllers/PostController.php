<?php

namespace App\Controllers;

use App\Category\CategoryService;
use App\Middlewares\LoginMiddleware;
use App\Post\Post;
use App\Post\PostService;
use App\User\UserService;
use Kernel\Controller\Controller;
use Kernel\Upload\Uploader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class PostController extends Controller
{
    public function index(Request $request, int $id)
    {
        $postService = new PostService($this->getDatabase());
        $postData = $postService->getPost($id);
        if (!$postData) {
            return new Response($this->initTwig('pages/error', [
                'error' => 'Такого поста не существует',
                'description' => "",
            ]));
        };

        $post = new Post($postData['id'], $postData['name'], $postData['category_name'], $postData['user_name'], $postData['image'], $postData['create_date'], $postData['update_date'], $postData['content']);


        return new Response(
            $this->initTwig("pages/post", [
                'post' => $post
            ])
        );
    }
    public function addPost(Request $request): Response
    {
        LoginMiddleware::checkLogin();
        $categoriesData = new CategoryService($this->getDatabase());

        $categories = [];
        foreach ($categoriesData->getCategories()['categories'] as $category) {
            $categories[$category['name']] = $category['id'];
        }

        $validator = Validation::createValidator();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();
        $form = $formFactory->createBuilder(FormType::class, null, [
            'action' => '/post/add',
            'method' => 'POST',
        ])
            ->setRequestHandler(new HttpFoundationRequestHandler())
            ->add('name', TextType::class, [
                'label' => 'Name',
                'constraints' => [
                    new NotBlank(['message' => 'Name cannot be blank.']),
                    new Length([
                        'min' => 3,
                        'max' => 50,
                        'minMessage' => 'Name must be at least 3 characters.',
                        'maxMessage' => 'Name cannot exceed 50 characters.',
                    ]),
                ]
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => $categories, // Список категорий
                'constraints' => [
                    new NotBlank(['message' => 'Please select a category.']),
                ],
            ])
            ->add('image', FileType::class, [
                'label' =>  'Изображение',
                'required' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Пожалуйста, загрузите изображение в формате JPEG, PNG или GIF.',
                    ])
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content'
            ])
            ->add('submit', SubmitType::class, ['label' => 'Create'])
            ->getForm();

        $form->handleRequest($request);

        return new Response($this->initTwig('pages/admin/form', [
            'form' => $form->createView(),
            'heading' => 'Create post',
        ]));
    }

    public function savePost()
    {

        # FIXME избежать инстанс сервиса
        $postService = new PostService($this->getDatabase());
        $userService = new UserService($this->getDatabase());
        $userId = $userService->getUser($this->session->getSession()['user'])['id'];
        $postData = $this->getRequest()->getPost();
        if ($this->getRequest()->getFiles()['form']['name']['image']) {

            $uploader = new Uploader(__DIR__ . '/../../public/uploads');

            $postData['form']['image'] = $uploader->upload($this->getRequest()->getFiles()['form']);
        }

        $postData['form']['user'] = $userId;
        $postService->addPost($postData);
        $this->redirect('/');
    }

    public function editPost(Request $request, int $id)
    {
        # FIXME избежать инстанс сервиса
        $postService = new PostService($this->getDatabase());
        $postData = $postService->getPost($id);
        if (!$postData) {
            return new Response($this->initTwig('pages/error', [
                'error' => 'Такого поста не существует',
                'description' => "",
            ]));
        };
        $post = new Post($postData['id'], $postData['name'], $postData['category_name'], $postData['user_name'], $postData['image'], $postData['create_date'], $postData['update_date'], $postData['content']);

        $categoriesData = new CategoryService($this->getDatabase());

        $categories = [];
        foreach ($categoriesData->getCategories()['categories'] as $category) {
            $categories[$category['name']] = $category['id'];
        }


        if ($this->session->getSession()['user'] == $postData['user_name']) {
            $data = [
                'id' => $post->id,
                'name' => $post->name,
                'category' => $post->category,
                'content' => $post->content
            ];

            $validator = Validation::createValidator();

            $formFactory = Forms::createFormFactoryBuilder()
                ->addExtension(new ValidatorExtension($validator))
                ->getFormFactory();
            $form = $formFactory->createBuilder(FormType::class, $data, [
                'action' => '/update/post',
                'method' => 'POST',
            ])
                ->setRequestHandler(new HttpFoundationRequestHandler())
                ->add('name', TextType::class, [
                    'label' => 'Name',
                    'constraints' => [
                        new NotBlank(['message' => 'Name cannot be blank.']),
                        new Length([
                            'min' => 3,
                            'max' => 50,
                            'minMessage' => 'Name must be at least 3 characters.',
                            'maxMessage' => 'Name cannot exceed 50 characters.',
                        ]),
                    ]
                ])
                ->add('category', ChoiceType::class, [
                    'label' => 'Category',
                    'choices' => $categories, // Список категорий
                    'constraints' => [
                        new NotBlank(['message' => 'Please select a category.']),
                    ],
                ])
                ->add('image', FileType::class, [
                    'label' =>  'Изображение',
                    'required' => false,
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                                'image/gif',
                            ],
                            'mimeTypesMessage' => 'Пожалуйста, загрузите изображение в формате JPEG, PNG или GIF.',
                        ])
                    ],
                ])
                ->add('delete_image', CheckboxType::class, [
                    'label' => 'Удалить изображение',
                    'required' => false,
                ])
                ->add('content', TextareaType::class, [
                    'label' => 'Content'
                ])
                ->add('id', HiddenType::class)
                ->add('submit', SubmitType::class, ['label' => 'Update'])
                ->getForm();

            $form->handleRequest($request);

            return new Response($this->initTwig('pages/admin/form', [
                'form' => $form->createView(),
                'heading' => 'Edit post',
            ]));
        } else {
            return new Response($this->initTwig('pages/error', [
                'error' => 'Вы не автор поста',
                'description' => "Редактирование разрешено только автору поста",
            ]));
        }
    }

    public function updatePost()
    {
        # FIXME избежать инстанс сервиса
        $postService = new PostService($this->getDatabase());
        $postData = $this->getRequest()->getPost();

        if ($this->getRequest()->getFiles()['form']['name']['image']) {

            $uploader = new Uploader(__DIR__ . '/../../public/uploads');

            $postData['form']['image'] = $uploader->upload($this->getRequest()->getFiles()['form']);
        }
        $postService->updatePost($postData);
        $this->redirect("/post/{$this->getRequest()->getPost()['form']['id']}");
    }

    public function deletePost(Request $request, int $id)
    {

        $postService = new PostService($this->getDatabase());
        $postData = $postService->getPost($id);
        if ($this->session->getSession()['user'] == $postData['user_name']) {
            $postService->deletePost($id);
            $this->redirect("/");
            exit();
        } else {
            return new Response($this->initTwig('pages/error', [
                'error' => 'Вы не автор поста или поста не существует',
                'description' => "Удалить пост может только его автор",
            ]));
        }
    }

    public function search(Request $request)
    {

        $page = (int)($request->query->get('page', 1)); // Текущая страница
        $itemsPerPage = 4;
        # FIXME избежать инстанс сервиса
        $postService = new PostService($this->getDatabase());
        $postsData = $postService->postSearch($page, $itemsPerPage, $this->getRequest()->get()['search']);
        if ($postsData == false) {
            return new Response($this->initTwig("pages/home", [
                'title' => 'Search Page',
            ]));
        }
        $posts = [];

        foreach ($postsData['posts'] as $postData) {
            $posts[] = new Post($postData['id'], $postData['name'], $postData['category_id'], $postData['user_id'], $postData['image'], $postData['create_date'], $postData['update_date'], $postData['content']);
        }

        $totalPosts = $postsData['total'];
        $totalPages = (int)ceil($totalPosts / $itemsPerPage);

        return new Response($this->initTwig("pages/home", [
            'title' => 'Search Page',
            'posts' => $posts,
            'currentPage' => $page,
            'totalPages' => $totalPages,
        ]));
    }
}