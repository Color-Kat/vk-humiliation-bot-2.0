<?php

namespace app\controllers;

use \app\core\Controller;

class AccountController extends Controller
{
    public function loginAction()
    {
        // $this->view->redirect('/account/registrati on');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->view::message('success', 'you are logged in');
        } else
            $this->view->render('Login page');
    }

    public function registrationAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->view::location('/account/login');
        } else
            $this->view->render('registration page');
    }
}
