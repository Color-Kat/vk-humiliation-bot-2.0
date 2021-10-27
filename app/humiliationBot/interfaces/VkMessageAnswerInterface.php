<?php

// Привет, ты скотина
// Привет - ответить на Привет
// ответить на ты скотина

interface VkMessageAnswerInterface extends \VkMessageInterface
{
    /**
     * парсит сообщение пользователя
     *
     * @param string $message
     * @return mixed
     */
    public function parse(mixed $data): mixed;

}