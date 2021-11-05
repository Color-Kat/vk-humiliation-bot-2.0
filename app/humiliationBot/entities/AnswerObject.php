<?php

namespace humiliationBot\entities;

use humiliationBot\traits\PatternProcessingTrait;

/**
 * class for work with ONLY array answers
 */
class AnswerObject
{
    // methods to processing answer pattern
    use PatternProcessingTrait;

    /**
     * @var array original answer array
     */
    private array $answerArrOriginal;

    /**
     * @var string original answer type (string or array)
     */
    public string $answerType = "array";

    /**
     * @var string string with regex pattern to match
     */
    private string $pattern;


    //TODO !!! ANSWER !!!
    /**
     * @var string|AnswerObject[] answer options
     */
    private $messages;

    /**
     * @var bool is need to work with next messages
     */
    private bool $with_prev_messages;

    /**
     * @var string id that allows to reply this message by answer from $next
     */
    private string $with_prev_mess_id;

    /**
     * @var string[] array of string with function names that we need to call
     */
    private array $execFunc;

    /**
     * @var array array with condition to check when answer matching
     */
    private array $condition;

    //TODO !!! ANSWER !!!
    /**
     * @var string|AnswerObject[] answer option when replying to a prev message
     */
    private $next;

    public function __construct($answerArr){
        $this->answerArrOriginal = $answerArr;

        $this->initAnswerObj($answerArr);
    }

    private function initAnswerObj(array $answerArrOriginal){
        $this->pattern = $this->patternProcessing($answerArrOriginal['pattern']);
        $this->message = new \stdClass($answerArrOriginal['messages']); // TODO Answer[] class
        $this->with_prev_messages = $answerArrOriginal['with_prev_messages'];
        $this->with_prev_mess_id = $answerArrOriginal['with_prev_mess_id'];
        $this->execFunc = $answerArrOriginal['execFunc'];
        $this->condition = $answerArrOriginal['condition'];
    }

    public function getMatch(){

    }
}