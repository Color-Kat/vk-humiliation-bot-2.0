<?php

namespace app\controllers;

use app\core\Controller;
use humiliationBot\Bot;

class BotController extends Controller
{
    protected $modelName = 'user';

    public function __construct($route)
    {
        parent::__construct($route);
    }

    public function indexAction()
    {
        // TODO сделать контроллер для автоматического создания bigDictionary
        $data = json_decode(file_get_contents('php://input'));

//        $user = $this->model->addUser(12345678, 'Олег', 'Закадычный');
//        $user = $this->model->getUser(15);
//        $user = $this->model->set_prev_message_id(15, 'apchhi');
//        $user = $this->model->deleteUser(16);
//        $user = $this->model->addUser(41846124, 'Густов', 'Ган Христиан');


        echo (new Bot($data))->run();
    }
}