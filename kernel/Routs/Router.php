<?php

namespace Kernel\Routs;
use App\Controllers\HomeController;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Router
{

    public RouteCollection $routeCollection;

    public RequestContext $requestContext;

    public UrlMatcher $urlMatcher;

    public Array $arrayRoutes;


    public function getArrayRoutes() {
        $this->arrayRoutes = require __DIR__ . "/../../config/routes.php";
        return $this->arrayRoutes;
    }

    public function getRouteCollection(): RouteCollection
    {

        // Коллекция маршрутов
        $this->routeCollection = new RouteCollection();

        foreach ($this->getArrayRoutes() as $route) {
            $this->routeCollection->add($route["name"], $route["route"]);
        }
        return $this->routeCollection;
    }


    public function dispatch()
    {
        // Контекст запроса
        $this->requestContext = new RequestContext();
        $request = Request::createFromGlobals();
        $this->requestContext->fromRequest($request);

// Создаем объект для поиска маршрутов
        $this->urlMatcher = new UrlMatcher($this->getRouteCollection(), $this->requestContext);

        try {
            // Ищем маршрут
            $parameters = $this->urlMatcher->match($request->getPathInfo());
            $controller = $parameters['_controller'];

            // Вызываем контроллер
            if (is_array($controller)) {
                [$class, $method] = $controller;
                $instance = new $class();
                $response = $instance->$method();
            } else {
                throw new \Exception('Invalid controller');
            }
        } catch (\Symfony\Component\Routing\Exception\ResourceNotFoundException $e) {
            $response = new Response('<h1>404 Not Found</h1>', 404);
            $response->send();
        } catch (\Exception $e) {
            $response = new Response('<h1>An error occurred</h1>', 500);
            $response->send();
        }

    }



}