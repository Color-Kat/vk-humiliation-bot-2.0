<?php

namespace app\core;

use \app\lib\Db;

abstract class Model extends Db
{
    /**
     * @var Db instance of Db class
     */
    protected $db;

    public function __construct()
    {
        parent::__construct();
    }
}
