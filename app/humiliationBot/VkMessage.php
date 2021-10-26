<?php

namespace app\humiliationBot;

class VkMessage implements \VkMessageInterface
{
    private $request_params = [];

    public function __construct($user_id)
    {
        // set standard options
        $this->request_params['user_id'] = $user_id;
        $this->request_params['access_token'] = bot_env('VK_TOKEN');
        $this->request_params['v'] = '5.130';
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

    /**
     * build query and send it to vk api
     */
    public function sendMessage(): bool
    {
        // send request
        file_get_contents(
            'https://api.vk.com/method/messages.send?' .
            http_build_query($this->request_params)
        );

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
