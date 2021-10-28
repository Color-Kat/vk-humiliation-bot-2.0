<?php

namespace humiliationBot\interfaces;

interface VkMessageInterface
{
    public function setMessage(string $message): void;

    public function setUserId(float $user_id): void;

    public function setReplyTo(float $reply_to): void;

    public function setSticker(float $stickerId): void;

    /**
     * build query and send it to vk api
     */
    public function sendMessage(): bool;
}