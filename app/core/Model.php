<?php

namespace app\core;

use \app\lib\Db;


abstract class Model
{
    /**
     * @var Db instance of Db class
     */
    protected $db;

    public function __construct()
    {
        $this->connectDb();
    }

    private function connectDb()
    {
        $this->db = $this->db ?? new Db();
    }
}
