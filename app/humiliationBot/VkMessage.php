<?php

namespace humiliationBot;

use humiliationBot\interfaces\VkMessageInterface;
use humiliationBot\traits\Vk;
use humiliationBot\traits\VkObjectParserTrait;
use VK\Client\VKApiClient;
use app\lib\Log;

class VkMessage implements VkMessageInterface
{
    use Vk;
    use VkObjectParserTrait;

    protected array $request_params = [];
    protected VKApiClient $vk;

    public function __construct($data)
    {
        // save data via trait method
        $this->setData($data);

        // init vk api class
        $this->setVk(new VKApiClient());

        // set standard options
        $this->request_params['user_id'] = $this->getUserId();
        $this->request_params['random_id'] = 0;
        $this->request_params['v'] = '5.131';
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

        $this->vk->messages()->send($this->access_token, $this->request_params);

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
