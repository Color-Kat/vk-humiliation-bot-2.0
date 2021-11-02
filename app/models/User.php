<?php

namespace app\models;

use \app\core\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * @param $user_id  int vk user_id
     * @param $name string user's name
     * @param $last_name string user's lastname
     * @return array|false
     */
    public function addUser($user_id, $name, $last_name) {
        return $this->db->query(
            "INSERT INTO users (user_id, name, lastname) VALUES (:user_id, :name, :last_name)",
            compact('user_id', 'name', 'last_name')
        );
    }

    /**
     * get all user's info from db
     *
     * @param $user_id int vk user_id
     * @return array|false
     */
    public function getUser(int $user_id) {
        return $this->select('*')
            ->where('id = :id')
            ->execute([
                'id' => $user_id
            ]);
    }


}
