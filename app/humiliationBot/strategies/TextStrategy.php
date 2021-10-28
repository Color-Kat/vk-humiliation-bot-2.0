<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\VkMessage;

class TextStrategy extends VkMessage implements VkMessageAnswerInterface
{
    use DictionaryTrait;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse(){
        $this->loadDictionary('test');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function generateAnswer($messages): string{
        return $messages;
    }
}