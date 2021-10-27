<?php

namespace app\controllers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PageController
{
    public function indexAction()
    {
        // create a log channel
        $log = new Logger('test');
        $log->pushHandler(new StreamHandler(__DIR__ . '/answer.log', Logger::DEBUG));

        // add records to the log
        $log->info('logger');

        echo 'It is just page';
    }
}