<?php

namespace app\humiliationBot;

class Bot
{
    /**
     * @var mixed request data from vk
     */
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Check VK_SECRET_TOKEN
     *
     * @return bool
     */
    private function security(): bool
    {
        if (
            $this->data->secret !== bot_env('VK_SECRET_TOKEN') &&
            $this->data->type !== 'confirmation'
        ) return false;

        return true;
    }

    /**
     * @return string type of vk request
     */
    private function type(): string
    {
        return $this->data->type;
    }

    public function run(): string
    {
        if (!$this->security()) return 'nioh';

        switch ($this->type()) {
            case 'confirmation':
                return bot_env('VK_CONFIRMATION_CODE');

            case 'message_new':
                // Будем использовать паттерн стратегия
                // есть разные типы событий - новое сообщение, пересланное, лайк и тд.
                // у всех будет метод sendMessage, setMessage и тд. - все реализуют интерфейс VkMessageInterface
                // но только когда человек присылает сообщение или пересылает его и тд,
                // нужно его проанализировать и ответить,
                // а при лайке - просто ответить.
                // Значит будем использовать несколько типов "контекстов"
                // контекст Answer - анализирует, генерирут соощение и отправляет
                // контекст Tell - просто отправляет сообщение
                // и в эти "контексты" будут подсовываться разные стратегии:
                // в Answer - например, Message, ReplyMessage, MessageEdit
                // (они будет использовать разные словари для ответа)
                //


                // create response array
                $request_params = [
                    'user_id' => $this->data->object->message->from_id,
                    'message' => 'Я родился!',
                    'access_token' => bot_env('VK_TOKEN'),
                    'v' => '5.81'
                ];

                // send message
                file_get_contents('https://api.vk.com/method/messages.send?' . http_build_query($request_params));

                return 'ok';

//            case 'message_reply':
//                return 'ok';
//
//            case 'message_edit':
//                return 'ok';
//
//            case 'photo_comment_new':
//                return 'ok';
//
//            case 'wall_reply_new':
//                return 'ok';
//
//            case 'like_add':
//                return 'ok';

            default:
                return 'nioh';
        }
    }
}