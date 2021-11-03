<?php

namespace humiliationBot\traits;

use VK\Actions\Users;
use VK\Client\VKApiClient;

/**
 * trait for work with Vk api methods
 */
trait Vk
{
    protected VKApiClient $vk;
    protected string $access_token;
    protected string $secure_token;
    protected string $vk_token;
    private int $user_id;
    private array $user_fields;

    public function setVk(VKApiClient $vk, int $user_id)
    {
        $this->vk = $vk;
        $this->access_token = bot_env('VK_TOKEN');
        $this->secure_token = bot_env('VK_SECRET_TOKEN');
        $this->vk_token = bot_env('VK_TOKEN');

        $this->user_id = $user_id;
        $this->init_user_fields();
    }

    public function init_user_fields()
    {
        $this->user_fields = $this->vk->users()->get($this->access_token, [
            'user_ids' => $this->user_id,
            'fields' => ['first_name', 'first_name_gen', 'first_name_dat',
                         'first_name_acc', 'first_name_ins', 'first_name_abl', 'last_name',
                         'bdate', 'country', 'city', 'relation'
                ]
        ])[0];
    }

    /**
     * return name in $case
     *
     * @param string $case case for name
     * @return string name in $case
     */
    public function getName(string $case = 'nom'): string{
        switch ($case){
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
    public function getLast_name(): string{
        return $this->user_fields['last_name'];
    }

    /**
     * @return string birthdate
     */
    public function getBirth(): string{
        return $this->user_fields['bdate'];
    }

    /**
     * @return string country
     */
    public function getCountry(): string{
        return $this->user_fields['country'];
    }

    /**
     * @return string city
     */
    public function getCity(): string{
        return $this->user_fields['city'];
    }

    /**
     * @return string relation
     */
    public function getRelation(): string{
        // TODO сделать получение семейного положения
//        return $this->user_fields->relation;
        return 'безответно влюблён';
    }

    public function getMessagesHistory(int $user_id, int $count = 2){
        return $this->vk->messages()->getHistory($this->access_token, [
            'offset' => 0,
            'user_id' => $user_id,
            'count' => $count
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