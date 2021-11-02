<?php

namespace app\models;

use \app\core\Model;

class User extends Model
{
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    public function addUser($user_id, $name, $last_name) {
        return $this->db->query(
            "INSERT INTO users (user_id, name, last_name) VALUES (:user_id, :name, :last_name)",
            compact('user_id', 'name', 'last_name')
        );
    }
}
