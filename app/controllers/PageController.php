<?php

namespace app\controllers;

use app\lib\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PageController
{
    public function indexAction()
    {
        echo 'It is just page';
    }
}