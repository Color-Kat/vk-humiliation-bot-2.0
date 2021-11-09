<?php

namespace humiliationBot\traits;

use humiliationBot\Attachment;

/**
 * trait for convenient work with the Vk request object
 */
trait VkObjectParserTrait
{
    protected \stdClass $data;

    public function setData(\stdClass $data)
    {
        $this->data = $data;
    }

    /**
     * return user_id from Vk data object
     *
     * @return int user_id
     */
    public function getUserId(): int
    {
        return
            $this->data->object->message->from_id ??
            $this->data->object->user_id ??
            $this->data->object->from_id ??
            1;
    }

    /**
     * return user's message
     *
     * @return string message
     */
    public function getMessage(): string
    {
        return $this->data->object->message->text ?? false;
    }

    /**
     * return user's attachment
     *
     * @return mixed attachment
     */
    public function getAttachment()
    {
        print_r($this->data->object->message);
        echo 'HERE';
        // TODO сделать класс для работы с attachment
//        return $this->data->object->message->attachments ?? false;
        return new Attachment($this->data->object->message->attachments[0]);
    }

    /**
     * returns the message replied by the user
     *
     * @return mixed reply message
     */
    public function getReplyMessage()
    {
        // TODO сделать класс для работы с attachment
        return $this->data->object->message->reply_message ?? false;
    }

    /**
     * return type of vk request
     *
     * @return string type of vk request
     */
    private function type(): string
    {
        return $this->data->type;
    }

    /**
     * return current date in timestamp
     *
     * @return int date
     */
    public function getDateTimestamp(): int
    {
        return $this->data->object->message->date;
    }

    /**
     * returns the current time in hh: mm format
     *
     * @return string time in hh:mm format
     */
    public function getTime(): string
    {
        return date("H:i", $this->getDateTimestamp());
    }

    /**
     * return day
     *
     * @return string day
     */
    public function getDay(): string
    {
        return date("d", $this->getDateTimestamp());
    }
}