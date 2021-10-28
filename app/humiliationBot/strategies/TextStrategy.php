<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\VkMessage;

class TextStrategy extends VkMessage implements VkMessageAnswerInterface
{
    // methods for work with dictionaries
    use DictionaryTrait;

    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('simple');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse(){
        $prevMessageIdFromDB = 'little_train123';
        $messageByPrevMessage = $this->getPrevMessagesById($prevMessageIdFromDB);

        Log::info('$messageByPrevMessage: ', $messageByPrevMessage);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function generateAnswer($messages): string{
        return $messages;
    }
}