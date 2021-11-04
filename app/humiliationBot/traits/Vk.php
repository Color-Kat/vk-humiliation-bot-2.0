<?php

namespace humiliationBot\traits;

use app\models\User;
use DateTime;
use DateTimeZone;
use VK\Actions\Users;
use VK\Client\VKApiClient;
use VK\Exceptions\VKApiException;
use VK\Exceptions\VKClientException;

/**
 * trait for work with Vk api methods
 */
trait Vk
{
    /**
     * @var VKApiClient vk api instance
     */
    protected VKApiClient $vk;

    /**
     * @var string access token for vk api
     */
    protected string $access_token;
    protected string $secure_token;
    protected string $vk_token;

    /**
     * @var int $user_id user_id for api queries
     */
    private int $user_id;

    /**
     * @var array fields received via vk api
     */
    private array $user_fields;

    /**
     * @param VKApiClient $vk instance VkApiClient
     * @param int $user_id user_id for api queries
     */
    public function setVk(VKApiClient $vk, int $user_id)
    {
        $this->vk = $vk;
        $this->access_token = bot_env('VK_TOKEN');
        $this->secure_token = bot_env('VK_SECRET_TOKEN');
        $this->vk_token = bot_env('VK_TOKEN');

        $this->user_id = $user_id; // save user_id
        $this->init_user_fields(); // get and save user's info
    }

    /**
     * save user info
     */
    public function init_user_fields()
    {
        // get user's data
        $user = new User();
        $user_info = $user->getUser($this->user_id);

        if (!$user_info || !$user_info['user_info']) {
            echo 'VK API';

            // get data from vk api
            $this->user_fields = $this->vk->users()->get($this->access_token, [
                'user_ids' => $this->user_id,
                'fields'   => [
                    'first_name', 'first_name_gen', 'first_name_dat',
                    'first_name_acc', 'first_name_ins', 'first_name_abl', 'last_name',
                    'bdate', 'country', 'city', 'relation'
                ]
            ])[0];

            // and save data in DB as json
            $user->set_user_info($this->user_id, json_encode($this->user_fields, JSON_UNESCAPED_UNICODE));
        } else
            // else we have already received data
            $this->user_fields = json_decode($user_info['user_info'], JSON_UNESCAPED_UNICODE);

    }

    /**
     * return name in $case
     *
     * @param string $case case for name
     * @return string name in $case
     */
    public function getName(string $case = 'nom'): string
    {
        switch ($case) {
            case 'nom':
                return $this->user_fields['first_name'];
            case 'gen':
                return $this->user_fields['first_name_gen'];
            case 'dat':
                return $this->user_fields['first_name_dat'];
            case 'acc':
                return $this->user_fields['first_name_acc'];
            case 'ins':
                return $this->user_fields['first_name_ins'];
            case 'abl':
                return $this->user_fields['first_name_abl'];
        }

        return $this->user_fields['first_name'];
    }

    /**
     * @return string last name
     */
    public function getLast_name(): string
    {
        return $this->user_fields['last_name'];
    }

    /**
     * @return string birthdate
     */
    public function getBirth(): string
    {
        return $this->user_fields['bdate'];
    }

    /**
     * @return int age number
     */
    public function getAge(): int
    {
        $tz  = new DateTimeZone('Europe/Moscow');

        try {
            $birth = DateTime::createFromFormat('d.m.Y', $this->getBirth(), $tz);
            if(!$birth) $birth = DateTime::createFromFormat('d.m.Y', '01.01.2005', $tz);

            return $birth->diff(new DateTime('now', $tz))->y;
        } catch (\Exception $e) {
            return 16;
        }
    }

    /**
     * @return string country
     */
    public function getCountry(): string
    {
        return $this->user_fields['country']['title'] ?? 'плохой стране';
    }

    /**
     * @return string city
     */
    public function getCity(): string
    {
        return $this->user_fields['city']['title'] ?? 'хрен знает где';
    }

    /**
     * @return string relation
     */
    public function getRelation(): string
    {
        // TODO сделать получение семейного положения
//        return $this->user_fields->relation;
        return 'безответно влюблён';
    }

    public function getMessagesHistory(int $user_id, int $count = 2)
    {
        return $this->vk->messages()->getHistory($this->access_token, [
            'offset'  => 0,
            'user_id' => $user_id,
            'count'   => $count
        ]);
    }



//    public function isSubscribed(int $user_id): bool{
//        $subscriptions = $this->vk->users()->getSubscriptions($this->vk_token, [
//            'user_id' => $user_id,
//        ])['response']['items'];
//
//        foreach ($subscriptions as $groupItem) {
//            if($groupItem['id'] == GROUP_ID) return true;
//        }
//
//        return false;
//    }
}