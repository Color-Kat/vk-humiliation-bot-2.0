<?php

namespace humiliationBot\entities;

/**
 * class to work with any answers
 */
class Answers
{

    /**
     * @var array
     */
    private array $dictionary;

    private string $type;

    private $answerTree;

    /**
     * @var array array with variables to be substituted into a string
     */
    private array $wordbook = [];

    /**
     * @param array|string $dictionary array [type => answer (array or string)]
     * @param array $wordbook
     */
    public function __construct($dictionary, array $wordbook)
    {
        if (gettype($dictionary) == "string") $this->type = "string";
        if (gettype($dictionary) == "array") $this->type = "array";

        $this->dictionary = gettype($dictionary) == "string"
            ? ["messages" => (array) $dictionary]
            : $dictionary;

        // set wordbook
        $this->wordbook = $wordbook;
    }

    /**
     * build a tree of AnswerObjects and AnswerMessages recursively
     *
     * @param array|string $answers answer options
     * @return array tree
     */
    public function buildTree($answers): array
    {
        $tree = [];

        if (gettype($answers) == "string")
            $tree[] = new AnswerMessage($answers, $this->wordbook);

        if (gettype($answers) == "array") {
            foreach ($answers as $answer) {
                $tree[] = $this->buildTree($answer);
            }
        }

        return $tree;
    }


    /**
     * match STRICT answer by PATTERN and priority
     *
     * @param string $u_message user's message
     * @param string $messagesKey key of field with messages (answers, messages, next)
     * @return array|false answer array with "messages" field
     */
    public function getAnswer(string $u_message, string $messagesKey = "messages")
    {
        // if we have one string in answer - return answer array with messages -> str
        if($this->type == "string") return ['messages' => (array) $this->dictionary['messages']];

        $answers = $this->dictionary[$messagesKey] ?? false;

        if (!$answers) return false;

        // no pattern in string - return string
        if (gettype($answers) == "string") return ["messages" => (array) $answers];

        $match = false;
        $priority = 0;

        if (gettype($answers) == "array") {
            // iterate all answers and check matching and priority
            foreach ($answers as $answer) {
                // create AnswerObject from $answer
                $answer = new AnswerObject((array) $answer, $this->wordbook);

                // check match
                $m = $answer->getMatch($u_message, $priority);

                if ($m) $match = $m; // save if matching is true
            }
        }

        return $match;
    }

    /**
     * get answer array by prev_mess_id
     *
     * - at first try to get answer from "next" by pattern
     * - secondly try to get answer from "forced" if forced_left != 0
     * - third try to get answer from "forced_end"
     * - and return false if no answer is found
     *
     * @return array|false array with "messages" key
     */
    public function getAnswerByPrevMess(string $u_message, callable $forcedCounter)
    {
        // get strict match BY PATTERN
        $strictMatch = $this->getAnswer($u_message, 'next');
        if ($strictMatch) return (array) $strictMatch;

        // create dictionary copy to change it and return
        $dictionaryCopy = $this->dictionary;

        // ========== SIMPLE ANSWERS ========== //
        // no answer by pattern
        // search answers without pattern
        $simpleAnswer = $this->getSimpleAnswer('next');
        if ($simpleAnswer) {
            $dictionaryCopy['messages'] = (array) $simpleAnswer;
            return $dictionaryCopy;
        }
        // ------------------------------------ //

        // ========== FORCED ANSWERS ========== //
        // if no simple answers - return forced messages
        if(
            isset($this->dictionary['forced']) &&
            call_user_func($forcedCounter, 'get') > 0 &&
            $this->checkCondition($this->dictionary['forced']) // check is condition met in forced
        ){
            call_user_func($forcedCounter, 'decrease');

            // change messages in answer array
            $dictionaryCopy['messages'] = (array) $this->dictionary['forced'];
            $dictionaryCopy['doAction'] = ["savePrevMessId" => true]; // set action to don't reset prev_mess_id in db
            return $dictionaryCopy;
        }
        // ----------------------------------- //

        //  ========== FORCED_END ANSWERS ========== //
        // if forced doesn't match condition or forced_left count is 0
        if(
            isset($this->dictionary['forced_end']) &&
            $this->checkCondition($this->dictionary['forced_end']) // check is condition met in forced
        ){
            call_user_func($forcedCounter, 'reset');

            // change messages in answer array
            $dictionaryCopy['messages'] = (array) $this->dictionary['forced_end'];
            return $dictionaryCopy;
        }
        // ----------------------------------------- //

        // no any answers - return false
        return false;
    }

    /**
     * check is condition met in $answer
     *
     * @param mixed $answer answer to check condition
     * @return bool is condition met
     */
    private function checkCondition($answer): bool{
        // check condition if answer is array
        if (isset($answer['condition'])) {
            $answerObj = new AnswerObject($answer, $this->wordbook);

            // condition is not met
            if(!$answerObj->checkCondition()) return false;
        }

        // all right! Condition is met
        return true;
    }

    /**
     * get "simple" answers from $answers
     * simple answer - answer without "pattern" field
     *
     * @param string $messagesKey string with key of field with messages where we need to find simple answers
     * @return array simple answers without pattern
     */
    private function getSimpleAnswer(string $messagesKey = "next"): array
    {
        $answers = (array) $this->dictionary[$messagesKey];

        echo '$answers'; print_r($answers);

        $simpleAnswers = [];

        foreach ($answers as $answer) {
            if (!isset($answer['pattern'])) {
                // check condition if answer is array
//                if (isset($answer['condition'])) {
//                    $answerObj = new AnswerObject($answer, $this->wordbook);
//
//                    // continue because condition is not met
//                    if(!$answerObj->checkCondition()) continue;
//                }

                if(!$this->checkCondition($answer)) continue;

                $simpleAnswers[] = $answer;
            }
        }

        return $simpleAnswers;
    }
}