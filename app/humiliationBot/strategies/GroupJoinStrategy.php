<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class GroupJoinStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
//        $this->loadDictionary('group_join');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        // user join the group
        // we don't need to parse it
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getAnswerMessage($messages): string
    {
        $this->subscribe();

        return $this->generateStandardAnswer('group_join');
    }
}