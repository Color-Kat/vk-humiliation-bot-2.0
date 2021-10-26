<?php

interface VkMessageAnswerInterface extends \VkMessageInterface
{
    /**
     * парсит сообщение пользователя
     * @param string $message
     */
    public function parse(mixed $data): void;

}