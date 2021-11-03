<?php

namespace humiliationBot\traits;

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
        return $this->data->object->message->from_id;
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
        // TODO сделать класс для работы с attachment
        return $this->data->object->message->attachment ?? false;
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