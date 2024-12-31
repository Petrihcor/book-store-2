<?php

namespace App\Controllers;

use App\Category\CategoryService;
use App\Post\PostService;
use App\User\User;
use App\User\UserService;
use Kernel\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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

class PostController extends Controller
{
    public function addPost(Request $request): Response
    {

        $categoriesData = new CategoryService($this->getDatabase());

        $categories = [];
        foreach ($categoriesData->getCategories() as $category) {
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
        $postData['form']['user'] = $userId;
        $postService->addPost($postData);
        $this->redirect('/');
    }
}