<?php

namespace app\core;

use app\core\View;

abstract class Controller
{
    /**
     * @var array list of controller and action names
     */
    protected $route;

    /**
     * @var View instance of View class 
     */
    protected $view;

    /**
     * @var $model instance of model class 
     */
    protected $model;

    public function __construct($route)
    {
        // route of this controller
        $this->route = $route;

        // View instance
        $this->view = new View($route);

        // load model
        $modelMame = $this->loadModel($route['controller']);
        if ($modelMame) $this->model = new $modelMame();
    }

    /**
     * @param string $name name of model class
     * @return string|bool class namespace or false
     */
    public function loadModel($name)
    {
        // path to file
        $path = APP . '/models/' .  ucfirst($name) . '.php';

        // namespace of model class
        $namespace = 'app\models\\' .  ucfirst($name);

        // error if class not found
        // if (!class_exists($path)) {
        // if (IS_DEV) throw new \Exception("class [" . ucfirst($name) . "] not found [$path]");
        // else $this->view::errorCode(500);
        // }

        if (file_exists($path) && class_exists($namespace))
            return $namespace;
        else return false;
    }
}
