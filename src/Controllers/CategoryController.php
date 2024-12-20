<?php

namespace App\Controllers;

use App\Category\Category;
use App\Category\CategoryService;
use Kernel\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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

        return new Response($this->initTwig('pages/admin/addCategory', [
            'form' => $form->createView(),
            'heading' => 'Create category',
        ]));
    }

    public function saveCategory()
    {

        # FIXME избежать инстанс сервиса
        $categoryservice = new CategoryService($this->getDatabase());
        $categoryservice->addCategory($this->getRequest()->getPost());
    }

    public function showCategories()
    {

        $categoriesData = new CategoryService($this->getDatabase());

        $categories = [];
        foreach ($categoriesData->getCategories() as $category) {
            $categories[] = new Category($category['id'], $category['name'], $category['description']);
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
        $category = new Category($categoryData['id'],$categoryData['name'],$categoryData['description']);
        $data = [
            'category' => $category
        ];

        echo $this->initTwig("pages/category", $data);
    }
}