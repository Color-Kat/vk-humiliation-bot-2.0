<?php

namespace app\controllers;

use app\humiliationBot\Bot;

class BotController
{
    public function indexAction()
    {
        $data = json_decode(file_get_contents('php://input'));

        echo (new Bot($data))->run();
    }
}