<?php

namespace app\controllers;

use app\core\Controller;
use app\lib\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PageController extends Controller
{
    public function indexAction()
    {
        echo 'It is just page';
    }
}