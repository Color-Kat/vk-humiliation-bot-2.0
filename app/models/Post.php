<?php

namespace app\models;

use \app\core\Model;

class Post extends Model
{
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    public function getProducts() {
        return $this->db->query('SELECT * FROM products');
    }

    public function getProduct(int $id) {
        $params = ['id' => $id];
        return $this->db->query('SELECT * FROM products WHERE id=:id', $params);
    }
}
