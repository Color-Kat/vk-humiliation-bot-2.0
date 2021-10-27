<?php

namespace app\controllers;

use app\lib\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PageController
{
    public function indexAction()
    {
        Log::info('Logger class 2');

        echo 'It is just page';
    }
}