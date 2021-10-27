<?php

namespace app\controllers;

use humiliationBot\Bot;

class BotController
{
    public function indexAction()
    {
        $data = json_decode(file_get_contents('php://input'));

        echo (new Bot($data))->run();
    }
}