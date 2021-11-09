<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\entities\Answers;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class TextStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('parts');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        // ===== by PREV_MESS_ID ===== //
        // get match by user's message and answer with_prev_mess_id
        $answerById = $this->getAnswer_by_prev_mess_id();

        if ($answerById)
            return (new Answers($answerById, $this->wordbook))
                ->getAnswerByPrevMess(
                    $this->getMessage(),
                    [$this, "forcedCounter"]
                );

        // ===== by MESSAGE MATCH ===== //
        // get messages by match user's message and dictionary
        return (new Answers($this->dictionary, $this->wordbook))
            ->getAnswer($this->getMessage(), 'answers');
    }

    /**
     * @inheritdoc
     */
    public function getAnswerMessage($messages): string
    {

        if (!$messages) return $this->generateStandardAnswer();
        else return $this->generateAnswerMessage($messages);
    }
}