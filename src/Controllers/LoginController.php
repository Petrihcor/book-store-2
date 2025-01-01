<?php

namespace App\Controllers;


use App\User\UserService;
use Kernel\Controller\Controller;
use Kernel\Session;
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


class LoginController extends Controller
{
    public function index(Request $request): Response
    {

        $validator = Validation::createValidator();

// Создаем фабрику форм с подключением валидатора
        $formFactory = Forms::createFormFactoryBuilder()
            ->addExtension(new ValidatorExtension($validator))
            ->getFormFactory();
        $form = $formFactory->createBuilder(FormType::class, null, [
            'action' => '/login',
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
            ->add('submit', SubmitType::class, ['label' => 'Login'])
            ->getForm();


        $form->handleRequest($request);

        return new Response($this->initTwig('pages/login', [
            'form' => $form->createView(),
            'heading' => 'Login',
        ]));
    }

    public function login()
    {
        # FIXME избежать инстанс сервиса
        $userservice = new UserService($this->getDatabase());
        $userData = $this->getRequest()->getPost();
        if ($userservice->checkUser($userData['form'])){

            $this->session->setSession("user", $this->getRequest()->getPost()['form']['name']);
            $this->redirect('/');
            exit;
        } else {
            # TODO сделать норм отображение ошибки аутентификации
            dd('пользователь не найден');
        }
    }

    public function logout()
    {
        $this->session->destroySession();
        $this->redirect('/');
        exit;
    }

}