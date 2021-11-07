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
     * @return mixed
     */
    public function parse();

//    /**
//     * do other logic in answerArray like:
//     *  - execute logic from $answerArr["doAction"] if parser return "doActions"
//     *  - update prev_mess_id in db
//     *  - exec functions
//     *
//     * @param array $answerArr
//     * @return mixed
//     */
//    public function doActions(array $answerArr);

    /**
     * generate message from $answerArr
     *
     * @param array $messages array with messages to create message
     *
     * @return string generated answer
     */
    public function generateAnswer(array $messages): string;
}