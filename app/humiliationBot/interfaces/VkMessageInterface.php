<?php

namespace humiliationBot\interfaces;

interface VkMessageInterface
{
    /**
     * set message to send to the user
     *
     * @param string $message
     */
    public function setMessage(string $message): void;

    /**
     * set vk user's id to receive message
     *
     * @param int $user_id
     */
    public function setUserId(int $user_id): void;

    public function setReplyTo(int $reply_to): void;

    /**
     *  send sticker to user by $sticker_id
     *
     * @param int $stickerId
     */
    public function setSticker(int $stickerId): void;

//    public function setPhoto(float $stickerId): void;

    /**
     * build query and send it to vk api
     */
    public function sendMessage(): bool;
}