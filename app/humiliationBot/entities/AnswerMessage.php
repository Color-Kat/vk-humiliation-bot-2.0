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
     * @var array array with variables to be substituted into a string
     */
    private array $wordbook = [];

    /**
     * @var string original answer type (string or array)
     */
    public string $answerType = "string";

    public function __construct($answerStr, array $wordbook){
        $this->answerMessage = $answerStr;
        $this->wordbook = $wordbook; // set wordbook
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