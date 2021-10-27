<?php

namespace humiliationBot\strategies;

use humiliationBot\interfaces\VkMessageAnswerInterface;
use humiliationBot\VkMessage;

class TextStrategy extends VkMessage implements VkMessageAnswerInterface
{
    /**
     * @inheritdoc
     */
    public function parse($data){
        return true;
    }

    /**
     * @inheritdoc
     */
    public function generateAnswer($messages): string{
        return $messages;
    }
}