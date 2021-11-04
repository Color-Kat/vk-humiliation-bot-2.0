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
    public function generateAnswer($messages): string
    {
        $this->unsubscribe();

        // we can't send messages when user joins or leaves
        // but i am trying;)
        return 'Спасибо за подписку)';
    }
}