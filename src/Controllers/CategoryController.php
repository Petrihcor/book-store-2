<?php

namespace App\Controllers;

use App\Category\Category;
use App\Category\CategoryService;
use App\Middlewares\LoginMiddleware;
use App\User\UserService;
use Kernel\Controller\Controller;
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
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;

class CategoryController extends Controller
{
    public function addCategory(Request $request): Response
    {

        LoginMiddleware::checkLogin();
        $validator = Validation::createValidator();

        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();
        $form = $formFactory->createBuilder(FormType::class, null, [
            'action' => '/category/add',
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
            ->add('description', TextareaType::class, [
                'label' => 'Description'
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Create'])
            ->getForm();

        $form->handleRequest($request);

        return new Response($this->initTwig('pages/admin/form', [
            'form' => $form->createView(),
            'heading' => 'Create category',
        ]));
    }

    public function saveCategory()
    {

        # FIXME избежать инстанс сервиса
        $userService = new UserService($this->getDatabase());
        $userId = $userService->getUser($this->session->getSession()['user'])['id'];
        $categoryservice = new CategoryService($this->getDatabase());
        $categoryData = $this->getRequest()->getPost();
        $categoryData['form']['user'] = $userId;

        $categoryservice->addCategory($categoryData);
        $this->redirect('/categories');
    }

    public function showCategories()
    {

        $categoriesData = new CategoryService($this->getDatabase());

        $categories = [];
        foreach ($categoriesData->getCategories() as $category) {
            $categories[] = new Category($category['id'], $category['name'], $category['user_id'], $category['description'], $category['create_date'], $category['update_date']);
        }
        $data = [
            'title' => 'Categories',
            'categories' => $categories
        ];

        echo $this->initTwig("pages/categories", $data);
    }

    public function catPage(Request $request, int $id)
    {

        $categoryService = new CategoryService($this->getDatabase());
        $categoryData = $categoryService->getCategory($id);
        $category = new Category($categoryData['id'], $categoryData['name'], $categoryData['user_name'], $categoryData['description'], $categoryData['create_date'], $categoryData['update_date']);

        $data = [
            'category' => $category,
        ];

        echo $this->initTwig("pages/category", $data);
    }

    public function editCategory(Request $request, int $id): Response
    {
        $categoryService = new CategoryService($this->getDatabase());
        $categoryData = $categoryService->getCategory($id);
        $category = new Category($categoryData['id'],$categoryData['name'], $categoryData['user_name'], $categoryData['description'], $categoryData['create_date'], $categoryData['update_date']);

        if ($this->session->getSession()['user'] == $categoryData['user_name']) {
            $data = [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description
            ];
            $validator = Validation::createValidator();

            $formFactory = Forms::createFormFactoryBuilder()
                ->addExtension(new ValidatorExtension($validator))
                ->getFormFactory();
            $form = $formFactory->createBuilder(FormType::class, $data, [
                'action' => '/update/category',
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
                ->add('description', TextareaType::class, [
                        'label' => 'Description'
                    ]
                )
                ->add('id', HiddenType::class)
                ->add('submit', SubmitType::class, ['label' => 'Update'])
                ->getForm();

            $form->handleRequest($request);

            return new Response($this->initTwig('pages/admin/form', [
                'form' => $form->createView(),
                'heading' => 'Edit category',
            ]));
        } else {
            return new Response($this->initTwig('pages/error', [
                'error' => 'Вы не автор категории',
                'description' => "Редактирование разрешено только автору категории",
            ]));
        }
    }

    public function updateCategory()
    {
        $categoryservice = new CategoryService($this->getDatabase());
        $categoryservice->updateCategory($this->getRequest()->getPost());
        $this->redirect("/category/{$this->getRequest()->getPost()['form']['id']}");
    }

    public function deleteCategory(Request $request, int $id)
    {
        $categoryService = new CategoryService($this->getDatabase());
        $categoryService->deleteCategory($id);
        $this->redirect("/categories");
    }
}