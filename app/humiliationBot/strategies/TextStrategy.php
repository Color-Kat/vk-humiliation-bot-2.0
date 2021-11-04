<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class TextStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('simple');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        // get prev message if from DB and get answer by this id
        $prevMessageId = $this->getPrevMessageId();
        echo $prevMessageId;

        // get ANSWER object from dictionary by prev_message_id
        $answerObj_byPrevMessId = $this->getAnswerByPrevMessId($prevMessageId);

        // get match by user's message and answer with_prev_message
        if ($answerObj_byPrevMessId)
            // and return $messages
            return $this->getMatchByPrevMess($this->getMessage(), $answerObj_byPrevMessId);

        // get messages by match user's message and dictionary
        return $this->getMatch($this->getMessage(), $this->dictionary);
    }

    /**
     * @inheritdoc
     */
    public function generateAnswer($messages): string
    {
        if (!$messages) return $this->generateStandardAnswer();
        else return $this->generateMessage($messages);
    }
}