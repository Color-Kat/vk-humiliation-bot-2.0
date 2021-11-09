<?php

namespace humiliationBot;

use app\lib\Log;
use app\models\User;
use humiliationBot\strategies\EditStrategy;
use humiliationBot\strategies\GroupJoinStrategy;
use humiliationBot\strategies\GroupLeaveStrategy;
use humiliationBot\strategies\TextStrategy;
use humiliationBot\traits\VkObjectParserTrait;

class Bot
{
    // methods to work with vk message object
    use VkObjectParserTrait;

    public function __construct($data)
    {
        $this->setData($data);
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
        ) {
            echo 'security';
            return false;
        }
        return true;
    }

    public function run(): string
    {
        if (!$this->security()) return 'nioh';

//         Будем использовать паттерн стратегия
//         есть разные типы событий - новое сообщение, пересланное, лайк и тд.
//         У всех будет метод sendMessage, setMessage и тд. - все реализуют интерфейс VkMessageInterface
//         Но только когда человек присылает сообщение или пересылает его и тд,
//         нужно его проанализировать и ответить,
//         а при лайке - просто ответить.
//         Значит будем использовать несколько типов "контекстов"
//         контекст AnswerContext - анализирует, генерирует сообщение и отправляет
//         контекст Tell - просто отправляет сообщение
//         и в эти "контексты" будут подсовываться разные стратегии:
//         в AnswerContext - например, Message, ReplyMessage, MessageEdit
//         (они будут использовать разные словари для ответа)
//
//         Должен быть объект пользователя с его возрастом, городом, именем, И ПРЕДЫДУЩИМИ СООБЩЕНИЯМИ
//
//         пришел запрос message_new
//         пользователь прислал сообщение
//         это может быть текст, фотка, вложение и тд
//         нужны классы, которые будут отвечать на эти разные типы сообщений
//         AnswerContext будет вызывать у этих объектов методы
//         PARSE - берет сообщение пользователя, берет нужный словарь, возвращает,
//         берет из БД with_prev_mess_id, если есть, и ищет по этому id ответ,
//         и отвечает согласно полю next и возвращает массив или строку messages
//
//         Потом нужно ответить пользователю, для этого будет метод GENERATE_ANSWER,
//         который принимает массив или строку messages,
//         и по правилам генерирует ответ
//         (например @insult вставить оскорбление или (a,b,c) - выбрать случайное слово)
//         Возвращает сам ответ
//
//         потом вызывается метод sendMessage с этим сообщением.
//
//                      *
//         Тааа-дааамм /

        switch ($this->type()) {
            case 'confirmation':
                return bot_env('VK_CONFIRMATION_CODE');

            case 'message_new':
                // create AnswerContext and strategy with vk data
                (new AnswerContext(
                    new TextStrategy($this->data)
                ))->answer();

                return 'ok';

            case 'message_edit':
                // create AnswerContext and strategy with vk data
                (new AnswerContext(
                    new EditStrategy($this->data)
                ))->answer();

                return 'ok';

            case 'group_join':
                (new AnswerContext(
                    new GroupJoinStrategy($this->data)
                ))->answer();

                return 'ok';

            case 'group_leave':
                (new AnswerContext(
                    new GroupLeaveStrategy($this->data)
                ))->answer();

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