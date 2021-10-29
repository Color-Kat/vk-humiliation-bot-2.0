<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\VkMessage;

class AbstractStrategy extends VkMessage
{
    // methods for work with dictionaries
    use DictionaryTrait;

    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function getMatch(string $message, array $dictionary){


        return 123;
    }
}