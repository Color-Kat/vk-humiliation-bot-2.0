<?php

namespace app\core;

class View
{
    /**
     * @var string path to view from views directory
     */
    private $path;

    /**
     * @var array list of controller name and action
     */
    private $route;

    /**
     * default layout
     */
    private $layout = 'default';

    public function __construct($route)
    {
        $this->route = $route;  // save controller and action names

        // save path to view from views directory
        $this->path = $route['controller'] . DIRECTORY_SEPARATOR . $route['action'];
    }

    /**
     * show View to user by current route
     * 
     * @param string $title page title
     * @param array $vars
     */
    public function render(string $title, array $vars = [])
    {
        // path to require view
        $filename = APP . '/views/' . $this->path . '.php';

        // check if the file exists
        if (!file_exists($filename)) {
            if (IS_DEV) throw new \Exception("View [$filename] is not found!");
            else self::errorCode(404);

            return false;
        }

        // extract array to variables 
        extract($vars);

        // copy file content in $content ($content is displayed in the view)
        ob_start();
        require $filename;
        $content = ob_get_clean();

        // require layout
        require APP . '/views/layouts/' . $this->layout . '.php';

        return true;
    }

    /**
     * show error page by http err code
     * 
     * @param int $code http error code
     */
    static public function errorCode($code)
    {
        http_response_code($code);

        $filename = APP . '/views/errors/' . $code . '.php';

        if (file_exists($filename)) require $filename;
        else if (IS_DEV) throw new \Exception('file [$filename] is not found!');

        exit;
    }

    /**
     * sends http response
     * 
     * @param string $status message status
     * @param string $message message
     */
    static public function message($status, $message)
    {
        echo json_encode(['status' => $status, 'body' => $message]);
    }

    /**
     * redirect to another page
     * 
     * @param string $url redirect url
     */
    static public function redirect(string $url)
    {
        header("location: $url");
        exit;
    }

    /**
     * redirect to another page by js
     * 
     * @param string $url redirect url
     */
    static public function location(string $url)
    {
        echo json_encode(['url' => $url]);
        exit;
    }
}
