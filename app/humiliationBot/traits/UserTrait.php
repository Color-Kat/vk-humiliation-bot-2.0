<?php

namespace humiliationBot\traits;

use app\models\User;

trait UserTrait
{
    /**
     * @var User instance of User model
     */
    private User $user;

    private int $user_id;

    /**
     * @var array user's data
     */
    protected array $userData;

    /**
     * @param int $user_id
     * @param array $data array with 'name' and 'lastname'
     */
    public function initUser(int $user_id, array $data = []){
        // create User instance
        $this->user = new User;

        $this->user_id = $user_id;

        // try to get user
        $this->userData = $this->user->getUser($user_id)[0];

        // add new user if it doesn't exist
        if(!$this->userData) {
            // add new user to db
            $this->user->addUser(
                $user_id,
                $data['name'] ?? 'Олег',
                $data['lastname'] ?? 'Безымянный'
            );

            // and get user's data
            $this->userData = $this->user->getUser($user_id)[0];
        }
    }

    /**
     * return current prev_message_id
     *
     * @return string prev_message_id
     */
    public function getPrevMessageId(): string{
        return $this->userData['prev_message_id'];
    }

    /**
     * set prev_message_id in DB
     *
     * @param string $with_prev_message_id
     */
    public function setPrevMessageId(string $with_prev_message_id){
        $this->user->set_prev_message_id($this->user_id, $with_prev_message_id);
    }

    /**
     * decrease forced_left in DB
     */
    public function decreaseForcedLeft(){
        $this->user->set_forced_left($this->user_id, $this->userData['forced_left'] - 1);
    }

    /**
     * decrease forced_left in DB
     */
    public function resetForcedLeft(){
        $this->user->set_forced_left($this->user_id, 3);
    }
}