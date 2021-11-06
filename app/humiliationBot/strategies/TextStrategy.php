<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\entities\AnswerFacade;
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

        // get ANSWER object from dictionary by prev_message_id
        $answerArr_byPrevMessId = $this->getAnswerByPrevMessId($prevMessageId);

        // get match by user's message and answer with_prev_message
        if ($answerArr_byPrevMessId)
            return (new AnswerFacade([
                "next" => $answerArr_byPrevMessId
            ]));
//
//        // get messages by match user's message and dictionary
//        return $this->getMatch($this->getMessage(), $this->dictionary);
    }

    /**
     * @inheritdoc
     */
    public function generateAnswer($messages): string
    {
//        if (!$messages) return $this->generateStandardAnswer();
//        else return $this->generateMessage($messages);
    }
}