<?php

namespace humiliationBot\entities;

/**
 * class to work with any answers
 */
class AnswerFacade
{

    private $dictionary;


    private $answerTree;

    /**
     * @var array array with variables to be substituted into a string
     */
    private array $wordbook = [];

    /**
     * @param array[]|string[] $dictionary array [type => answer (array or string)]
     * @param array $wordbook
     */
    public function __construct(array $dictionary, array $wordbook)
    {
        $this->dictionary = $dictionary;

        // set wordbook
        $this->wordbook = $wordbook;

//        $this->answerTree = $this->buildTree($answers);
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
        $answers = $this->dictionary[$messagesKey];

        if (!$answers) return false;

        // no pattern in string - return string
        if (gettype($answers) == "string") return ["messages" => $answers];

        $match = false;
        $priority = 0;

        if (gettype($answers) == "array") {
            // iterate all answers and check matching and priority
            foreach ($answers as $answer) {
                // create AnswerObject from $answer
                $answer = new AnswerObject($answer, $this->wordbook);

                // check match
                $m = $answer->getMatch($u_message, $priority);

                if ($m) $match = $m; // save if matching is true
            }
        }

        return $match;
    }

    /**
     * @return array|string
     */
    public function getAnswerByPrevMess(string $u_message, int $forced_left)
    {
        // get strict match BY PATTERN
        $strictMatch = $this->getAnswer($u_message, 'next');
        if ($strictMatch) return $strictMatch;

        // create dictionary copy to change it and return
        $dictionaryCopy = $this->dictionary['next'];

        // ========== SIMPLE ANSWERS ========== //
        // no answer by pattern
        // search answers without pattern
        $simpleAnswer = $this->getSimpleAnswer('next');
        if ($simpleAnswer) {
            $dictionaryCopy['messages'] = $simpleAnswer;
            return $dictionaryCopy;
        }
        // ------------------------------------ //

        // ========== FORCED ANSWERS ========== //
        // if no simple answers - return forced messages
        if(
            $this->dictionary['forced'] &&
            $forced_left > 0 &&
            $this->checkCondition($this->dictionary['forced']) // check is condition met in forced
        ){
            // TODO reduce forced_left

            // change messages in answer array
            $dictionaryCopy['messages'] = $this->dictionary['forced'];
            return $dictionaryCopy;
        }
        // ----------------------------------- //

        //  ========== FORCED_END ANSWERS ========== //
        // if forced doesn't match condition or forced_left count is 0
        if(
            $this->dictionary['forced_end'] &&
            $this->checkCondition($this->dictionary['forced_end']) // check is condition met in forced
        ){
            // TODO clear forced_left

            // change messages in answer array
            $dictionaryCopy['messages'] = $this->dictionary['forced_end'];
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
        $answers = $this->dictionary[$messagesKey];

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