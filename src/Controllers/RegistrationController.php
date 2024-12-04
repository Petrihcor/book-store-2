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

class RegistrationController extends Controller
{
    public function register(Request $request): Response
    {
        $formFactory = Forms::createFormFactory();
        $form = $formFactory->createBuilder(FormType::class)
            ->setRequestHandler(new HttpFoundationRequestHandler())
            ->setAction('/save-user')
            ->add('name', TextType::class, ['label' => 'Name'])
            ->add('password', PasswordType::class, ['label' => 'Password'])
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