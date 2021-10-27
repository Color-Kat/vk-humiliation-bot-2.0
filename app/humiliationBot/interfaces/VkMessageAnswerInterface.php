<?php

// Привет, ты скотина
// Привет - ответить на Привет
// ответить на ты скотина

namespace humiliationBot\interfaces;

interface VkMessageAnswerInterface extends VkMessageInterface
{
    /**
     * парсит сообщение пользователя
     *
     * @param string $message
     * @return mixed
     */
    public function parse($data);

    /**
     * @param string|array $messages array or string, where we need to create answer by template
     * @return string generated answer
     */
    public function generateAnswer($messages): string;
}