<?php

namespace humiliationBot;

use app\lib\Log;
use app\models\User;
use humiliationBot\strategies\AudioMessageStrategy;
use humiliationBot\strategies\AudioStrategy;
use humiliationBot\strategies\BigTextStrategy;
use humiliationBot\strategies\DocStrategy;
use humiliationBot\strategies\EditStrategy;
use humiliationBot\strategies\GroupJoinStrategy;
use humiliationBot\strategies\GroupLeaveStrategy;
use humiliationBot\strategies\LongTextStrategy;
use humiliationBot\strategies\MediumTextStrategy;
use humiliationBot\strategies\PhotoStrategy;
use humiliationBot\strategies\ReplyMessageStrategy;
use humiliationBot\strategies\StickerStrategy;
use humiliationBot\strategies\TextStrategy;
use humiliationBot\strategies\VideoStrategy;
use humiliationBot\strategies\WallPostStrategy;
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
                $messageType = $this->getAttachmentType();

                switch ($messageType) {
                    case 'text':
                        // create AnswerContext and strategy with vk data
                        (new AnswerContext(
                            new TextStrategy($this->data)
                        ))->answer();
                        break;

                    case 'photo':
                        // create AnswerContext and strategy with vk data to reply to PHOTO
                        (new AnswerContext(
                            new PhotoStrategy($this->data)
                        ))->answer();
                        break;

                    case 'reply_message':
                        // create AnswerContext and strategy with vk data to reply to REPLY_MESSAGE
                        if (rand(0, 7) == 0)
                            (new AnswerContext(
                                new ReplyMessageStrategy($this->data)
                            ))->answer();
                        else (new AnswerContext(
                                new TextStrategy($this->data)
                            ))->answer();
                        break;

                    case 'audio_message':
                        // reply to VOICE MESSAGE
                        (new AnswerContext(
                            new AudioMessageStrategy($this->data)
                        ))->answer();
                        break;

                    case 'sticker':
                        // reply to STICKERS, but sometimes send answer to text
                        if (rand(0, 3) == 3) (new AnswerContext(
                                new StickerStrategy($this->data)
                            ))->answer();
                        else (new AnswerContext(
                                new TextStrategy($this->data)
                            ))->answer();
                        break;

                    case 'wall':
                        // reply to WALL POST
                        (new AnswerContext(
                            new WallPostStrategy($this->data)
                        ))->answer();
                        break;

                    case 'video':
                        // reply to VIDEO
                        (new AnswerContext(
                            new VideoStrategy($this->data)
                        ))->answer();
                        break;

                    case 'audio':
                        // reply to AUDIO song
                        (new AnswerContext(
                            new AudioStrategy($this->data)
                        ))->answer();
                        break;

                    case 'doc':
                        // reply to Documents
                        (new AnswerContext(
                            new DocStrategy($this->data)
                        ))->answer();
                        break;

                    case 'medium_text':
                        // reply to text message with size > 130
                        (new AnswerContext(
                            new MediumTextStrategy($this->data)
                        ))->answer();
                        break;

                    case 'long_text':
                        // reply to text message with size > 300
                        (new AnswerContext(
                            new LongTextStrategy($this->data)
                        ))->answer();
                        break;

                    case 'big_text':
                        // reply to text message with size > 1000
                        (new AnswerContext(
                            new BigTextStrategy($this->data)
                        ))->answer();
                        break;

                    default:
                        // reply to standard answer if we can't define message type
                        (new AnswerContext(
                            new TextStrategy($this->data)
                        ))->answer();
                        break;
                }

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
