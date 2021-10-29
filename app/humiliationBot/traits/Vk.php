<?php

namespace humiliationBot\traits;

use VK\Client\VKApiClient;

/**
 * trait for work with Vk api methods
 */
trait Vk
{
    protected VKApiClient $vk;
    protected string $access_token;

    public function setVk(VKApiClient $vk)
    {
        $this->vk = $vk;
        $this->access_token = bot_env('VK_TOKEN');
    }
}