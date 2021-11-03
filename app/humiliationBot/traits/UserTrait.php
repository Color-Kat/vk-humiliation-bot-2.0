<?php

namespace humiliationBot\traits;

use app\models\User;

trait UserTrait
{
    /**
     * @var User instance of User model
     */
    protected $user;

    /**
     * @var array user's data
     */
    protected $userData;

    /**
     * @param int $user_id
     * @param array $data array with 'name' and 'lastname'
     */
    public function initUser(int $user_id, array $data = []){
        // create User instance
        $this->user = new User;

        // try to get user
        $this->userData = $this->user->getUser($user_id);

        // add new user if it doesn't exist
        if(!$this->userData) {
            // add new user to db
            $this->user->addUser(
                $user_id,
                $data['name'] ?? 'Олег',
                $data['lastname'] ?? 'Безымянный'
            );

            // and get user's data
            $this->userData = $this->user->getUser($user_id);
        }
    }
}