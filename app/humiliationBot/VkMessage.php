<?php

namespace humiliationBot;

use VK\Client\VKApiClient;
use app\lib\Log;
use humiliationBot\interfaces\VkMessageInterface;

class VkMessage implements VkMessageInterface
{
    protected \stdClass $data;
    protected array $request_params = [];
    private string $access_token;

    public function __construct($data)
    {
        $this->data = $data;
        $this->access_token = bot_env('VK_TOKEN');

        // set standard options
        $this->request_params['user_id'] = $this->getUserId();
        $this->request_params['random_id'] = 0;
        $this->request_params['access_token'] = $this->access_token;
        $this->request_params['v'] = '5.131';
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->data->object->message->from_id;
    }

    public function setMessage(string $message): void
    {
        $this->request_params['message'] = $message;
    }

    public function setUserId(float $user_id): void
    {
        $this->request_params['user_id'] = $user_id;
    }

    public function setReplyTo(float $reply_to): void
    {
        $this->request_params['reply_to'] = $reply_to;
    }

    public function setSticker(float $stickerId): void
    {
        $this->request_params['sticker_id'] = $stickerId;
    }

    /**
     * build query and send it to vk api
     */
    public function sendMessage(): bool
    {

//        Log::info($this->request_params['message']);

        $vk = new VKApiClient();
        $vk->messages()->send($this->access_token, $this->request_params);

//        $request_params = [
//            'user_id'      => $this->request_params['user_id'],
//            'message'      => 'Я родился!',
//            'access_token' => bot_env('VK_TOKEN'),
//            'v'            => '5.81'
//        ];
//
//        // send message
//        file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

        return true;
    }
}

//$request_params = [
//'user_id' => $this->data->object->message->from_id,
//'message' => 'Я родился!',
//'access_token' => bot_env('VK_TOKEN'),
//'v' => '5.81'
//];
//
//    // send message
//file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));
