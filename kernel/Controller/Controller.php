<?php

namespace Kernel\Controller;
use Kernel\Database\Database;
use Kernel\Http\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Bridge\Twig\Extension\TranslationExtension;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\Loader\ArrayLoader;

abstract  class Controller
{

    protected Request $request;
    protected Environment $twig;
    protected FilesystemLoader $loader;

    protected Database $database;


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


    protected function initTwig(string $name, array|null $data = null): string
    {
        $this->loader = new FilesystemLoader([
            __DIR__ . '/../../views',
            __DIR__ . '/../../vendor/symfony/twig-bridge/Resources/views/Form'
        ]);
        $this->twig = new Environment($this->loader);

        // Добавляем переводчик
        $translator = new Translator('en');
        $translator->addLoader('array', new ArrayLoader());

        // Добавляем расширение перевода
        $this->twig->addExtension(new TranslationExtension($translator));

        if (isset($data['form'])) {
            // Настройка Form Extension
            $defaultFormTheme = 'form_div_layout.html.twig';
            $formEngine = new TwigRendererEngine([$defaultFormTheme], $this->twig);
            $formRenderer = new FormRenderer($formEngine);

            $this->twig->addExtension(new FormExtension());
            $this->twig->addRuntimeLoader(new \Twig\RuntimeLoader\FactoryRuntimeLoader([
                FormRenderer::class => fn() => $formRenderer,
            ]));
        }

        return $this->twig->render("$name.html.twig", $data);
    }
}