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
        $this->loadDictionary('simple');

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
        $answerArr =  (new Answers($this->dictionary, $this->wordbook))
            ->getAnswer($this->getMessage(), 'answers');


        // ===== answer array by CHANCE ===== //
        $chanceAnswerArr = $this->chance();

        // chance works
        if (isset($chanceAnswerArr['messages'])) {
            if($chanceAnswerArr['chance'] > 90) return $chanceAnswerArr;

            // don't use chance if priority is higher than 50
            if(
                ($answerArr['priority'] ?? 0) <= 40 &&
                $chanceAnswerArr['chance'] > 24
            ) return $chanceAnswerArr;

            if (!$answerArr) return $chanceAnswerArr;
        }

        return $answerArr;
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