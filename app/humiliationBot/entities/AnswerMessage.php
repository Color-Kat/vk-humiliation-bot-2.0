<?php

namespace humiliationBot\entities;

use humiliationBot\traits\MessageProcessingTrait;
use humiliationBot\traits\PatternProcessingTrait;

/**
 * class for work with ONLY string answers
 */
class AnswerMessage
{
    // methods to processing string answer message
    use MessageProcessingTrait;

    /**
     * @var string original answer string
     */
    private string $answerMessage;

    /**
     * @var string original answer type (string or array)
     */
    public string $answerType = "string";

    public function __construct($answerArr){
        $this->answerMessage = $answerArr;
    }

    /**
     * process message:
     *  - variable substitution
     *  - function
     *
     * @return string final processed string message
     */
    public function getMessage(): string{
        return $this->messageProcessing($this->answerMessage);
    }
}