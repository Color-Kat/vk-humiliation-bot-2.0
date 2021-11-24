<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class GroupLeaveStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
//        $this->loadDictionary('group_leave');

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
        $this->unsubscribe();

        return $this->generateStandardAnswer('group_leave');
    }
}