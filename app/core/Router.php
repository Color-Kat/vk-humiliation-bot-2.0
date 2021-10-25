<?php

namespace app\core;

use app\core\View;

class Router
{
    /**
     * @var array $routes list of routes (route, method, controller, action)
     */
    protected $routes = [];

    /**
     * @var array $params current controller and action names
     */
    protected $currentRoute = [];

    public function __construct()
    {
        $arr = require APP . '/config/routes.php';
        foreach ($arr as $route) {
            $this->add($route);
        }
    }

    /**
     * format and add routes and params to propery $routes
     *
     * @param string $route url
     * @param array $params assocc arr with controller and action names
     */
    public function add($route): void
    {
        // add \ before /
        $route['route'] = str_replace('/', '\/', $route['route']);

        // remove last slash
        $route['route'] = strlen($route['route']) > 2 ?
                            preg_replace("/\/$/", "", $route['route']) :
                            $route['route'];

        $this->routes[] = $route;
    }

    /**
     * check if the current url matches any route
     *
     * @return bool
     */
    public function match(): bool
    {
        $url = $this->getUrl();

        foreach ($this->routes as $route) {
            $routMethod = strtolower($route['method']);
            $method = strtolower($_SERVER['REQUEST_METHOD']);

            // check are methods equal
            if ($routMethod != $method) continue;

            if (preg_match(
                '/^' . $route['route'] . '$/',
                $url,
                $matches
            )) {
                // save data about this route
                $this->currentRoute = $route;

                return true;
            }
        }

        return false;
    }

    /**
     * get url without ROOT_ROUTE (/2.0)
     *
     * @return string $url
     */
    public function getUrl(): string
    {
        $fullUrl = $_SERVER['REQUEST_URI'];
        $url = strpos($fullUrl, ROOT_ROUTE);

        return substr($fullUrl, 0, $url);
    }

    /**
     *
     */
    public function run()
    {
        // check if the url matches any route
        if ($this->match()) {
            // controller path
            $path = '\app\controllers\\' . ucfirst($this->currentRoute['controller']) . 'Controller';

            // class doesn't exist
            if (!class_exists($path)) {
                if (IS_DEV) throw new \Exception("Controller not found - [$path]");
                else View::errorCode(404);
                exit;
            }

            // action name
            $action = $this->currentRoute['action'] . 'Action';

            // method doesn't exist
            if (!method_exists($path, $action)) {
                if (IS_DEV) throw new \Exception("Method [$action] is not found in [$path]");
                else View::errorCode(500);
                exit;
            }

            // create controller and run action
            $controller = new $path($this->currentRoute);
            $controller->$action();
        } else {
            echo 'Route is not found!';
        }
    }
}
