<?php

namespace Kernel\Controller;
use App\User\User;
use Kernel\Database\Database;
use Kernel\Http\Request;
use Kernel\Session;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\Forms;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\FactoryRuntimeLoader;
use Symfony\Component\Translation\Loader\XliffFileLoader;
use Symfony\Component\Translation\Translator;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Validator\Validation;
abstract  class Controller
{

    protected Request $request;
    protected Environment $twig;
    protected FilesystemLoader $loader;

    protected Database $database;

    protected Session $session;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            $this->session = new Session();
        }
    }


    public function getRequest(): Request
    {
        $this->request = new Request();
        return $this->request;
    }



    public function getDatabase(): Database
    {
        # FIXME: нужно избавиться от необходимости постоянно обьявлять переменную config для класса Database
        $config = require __DIR__ . "/../../config/db.php";
        return $this->database = new Database($config);
    }

    private function getEnvironment()
    {
        $this->loader = new FilesystemLoader([
            __DIR__ . '/../../views',
            __DIR__ . '/../../vendor/symfony/twig-bridge/Resources/views/Form'
        ]);
        $this->twig = new Environment($this->loader);
    }

    private function getForm()
    {
        // the Twig file that holds all the default markup for rendering forms
// this file comes with TwigBridge
        $defaultFormTheme = 'form_div_layout.html.twig';

        $vendorDirectory = realpath(__DIR__.'/../vendor');
// the path to TwigBridge library so Twig can locate the
// form_div_layout.html.twig file
        $appVariableReflection = new \ReflectionClass('\Symfony\Bridge\Twig\AppVariable');
        $vendorTwigBridgeDirectory = dirname($appVariableReflection->getFileName());
// the path to your other templates
        $viewsDirectory = realpath(__DIR__.'/../views');


        $formEngine = new TwigRendererEngine([$defaultFormTheme], $this->twig);
        $this->twig->addRuntimeLoader(new FactoryRuntimeLoader([
            FormRenderer::class => function () use ($formEngine): FormRenderer {
                return new FormRenderer($formEngine);
            },
        ]));

// ... (see the previous CSRF Protection section for more information)


// adds the FormExtension to Twig
        $this->twig->addExtension(new FormExtension());

        // creates the Translator
        $translator = new Translator('en');
// somehow load some translations into it
        $translator->addLoader('xlf', new XliffFileLoader());
        $this->twig->addExtension(new TranslationExtension($translator));
    }

    protected function initTwig(string $name, array|null $data = null): string
    {
        $this->getEnvironment();

        if (isset($this->session->getSession()['user'])) {
            $user = new User($this->session->getSession()['user']['form']["name"]);
            $data['user'] = $user;
        }



        if (isset($data['form'])) {
            $this->getForm();
        }


        return $this->twig->render("$name.html.twig", $data);
    }
}