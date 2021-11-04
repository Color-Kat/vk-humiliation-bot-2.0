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
        $userData = $this->user->getUser($user_id);

        // add new user if it doesn't exist
        if(!$userData) {
            // add new user to db
            $this->user->addUser(
                $user_id,
                $data['name'] ?? 'Олег',
                $data['lastname'] ?? 'Безымянный'
            );

            // and get user's data
            $userData = $this->user->getUser($user_id);
        }

        $this->userData = $userData;
    }

    /**
     * return current prev_message_id
     *
     * @return string | false prev_message_id
     */
    public function getPrevMessageId(){
        return $this->userData['prev_message_id'] ?? false;
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

    /**
     * set isSubscribed field to true in db
     */
    public function subscribe(){
        $this->user->set_isSubscribed($this->user_id, true);
    }

    /**
     * set isSubscribed field to false in db
     */
    public function unsubscribe(){
        $this->user->set_isSubscribed($this->user_id, false);
    }
}