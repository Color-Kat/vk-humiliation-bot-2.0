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

    public function setVk(VKApiClient $vk)
    {
        $this->vk = $vk;
        $this->access_token = bot_env('VK_TOKEN');
        $this->secure_token = bot_env('VK_SECRET_TOKEN');
        $this->vk_token = bot_env('VK_TOKEN');
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