<?php

namespace App\Controllers;


use App\User\UserService;
use Kernel\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;


class RegistrationController extends Controller
{
    public function register(Request $request): Response
    {

        $validator = Validation::createValidator();

// Создаем фабрику форм с подключением валидатора
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();
        $form = $formFactory->createBuilder(FormType::class, null, [
            'action' => '/save-user',
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
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'constraints' => [
                    new NotBlank(['message' => 'Password cannot be blank.']),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Password must be at least 8 characters.',
                    ]),
                ],
                ])
            ->add('submit', SubmitType::class, ['label' => 'Register'])
            ->getForm();


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Handle form submission, e.g., save $data to the database
            return new Response('Registration successful!');
        }
        return new Response($this->initTwig('pages/registration', [
            'form' => $form->createView(),
            'heading' => 'Registration',
        ]));
    }

    public function saveUser()
    {
        # FIXME избежать инстанс сервиса
        # TODO валидация
        $userservice = new UserService($this->getDatabase());
        $userservice->addUser($this->getRequest()->getPost());
    }

    # TODO авторизация
}