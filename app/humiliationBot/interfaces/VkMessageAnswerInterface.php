<?php

// Привет, ты скотина
// Привет - ответить на Привет
// ответить на ты скотина

namespace humiliationBot\interfaces;

interface VkMessageAnswerInterface extends VkMessageInterface
{
    /**
     * parse user's message and try to get answer array
     *
     * @return array|false
     */
    public function parse();

    /**
     *
     *
     * @param array $answerArr
     * @return void
     */
    public function doActions(array $answerArr);

    /**
     * return answer array by chance or false
     *
     * @return array|false
     */
    public function chance();

    /**
     * get and process one string message from $messages
     *
     * @param array|false $messages array with messages to create message
     *
     * @return string generated answer
     */
    public function getAnswerMessage($messages): string;

    // ===== METHODS TO SEND OTHER TYPES OF MESSAGES ===== //

    /**
     * try to send sticker
     *  - get sticker id by pattern from messages - (sticker_strId)
     *  - get sticker id by strId from sticker_list.json
     *  - send sticker
     *
     * @param string $message
     */
    public function sticker(string $message): void;


    public function photo(string $message): void;
}