<?php

namespace app\models;

use \app\core\Model;

class User extends Model
{
    protected $table = 'users';

    /**
     * @param int $user_id vk user_id
     * @param string $name user's name
     * @param string $last_name user's lastname
     * @return array|false
     */
    public function addUser($user_id, $name, $last_name)
    {
        return $this->insert([
            ['user_id', $user_id],
            ['name', $name],
            ['lastname', $last_name]
        ])
            ->execute();
    }

    /**
     * get all user's info from db
     *
     * @param int $user_id vk user_id
     * @return array|false
     */
    public function getUser(int $user_id)
    {
        return $this->select('*')
            ->where('user_id = :user_id')
            ->execute([
                'user_id' => $user_id
            ])[0] ?? false;
    }

    /**
     * @param int $user_id vk user_id
     * @param string $prev_mess_id
     * @return array|false
     */
    public function set_prev_message_id(int $user_id, string $prev_mess_id)
    {
        return $this->update([
            ['prev_message_id', $prev_mess_id]
        ])
            ->where('user_id = :user_id')
            ->execute([
                'user_id' => $user_id
            ]);
    }

    /**
     * delete user from db by id
     *
     * @param int $user_id vk user's id
     * @return array|false
     */
    public function deleteUser(int $user_id) {
        return $this->delete()
            ->where('user_id = :user_id')
            ->execute([
                'user_id' => $user_id
            ]);
    }

    public function set_forced_left(int $user_id, int $forced_left){
        return $this->update([
            ['forced_left', $forced_left]
        ])
            ->where('user_id = :user_id')
            ->execute([
                'user_id' => $user_id
            ]);
    }

    /**
     * set user_info field in bd
     *
     * @param int $user_id vk user_id
     * @param string $user_info json data of user
     * @return array|false
     */
    public function set_user_info(int $user_id, string $user_info)
    {
        return $this->update([
            ['user_info', $user_info]
        ])
            ->where('user_id = :user_id')
            ->execute([
                'user_id' => $user_id
            ]);
    }
}
