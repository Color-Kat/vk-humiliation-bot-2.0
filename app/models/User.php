<?php

namespace app\models;

use \app\core\Model;

class User extends Model
{
    // public function __construct()
    // {
    //     parent::__construct();
    // }

    protected $table = 'users';

    public function addUser($user_id, $name, $last_name) {
        return $this->db->query(
            "INSERT INTO users (user_id, name, lastname) VALUES (:user_id, :name, :last_name)",
            compact('user_id', 'name', 'last_name')
        );
    }

    public function getUser($user_id) {
        return $this->select('name', 'lastname')
            ->where('id = :id')
            ->execute([
                'id' => $user_id
            ]);
    }
}
