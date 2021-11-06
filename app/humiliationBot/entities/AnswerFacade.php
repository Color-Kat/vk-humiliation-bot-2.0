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
     * @param array|string $answers
     * @param array $wordbook
     */
    public function __construct($dictionary, array $wordbook)
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
     * @return array|false|string answer string o
     */
    public function getAnswer(string $u_message, string $messagesKey)
    {
        $answers = $this->dictionary[$messagesKey];

        // no pattern in string - return string
        if(gettype($answers) == "string") return $answers;

        $match = false;
        $priority = 0;

        if (gettype($answers) == "array") {
            // iterate all answers and check matching and priority
            foreach ($answers as $answer) {
                // create AnswerObject from $answer
                $answer = new AnswerObject($answer, $this->wordbook);

                // check match
                $m = $answer->getMatch($u_message, $priority);

                if($m) $match = $m; // save if matching is true
            }
        }

        return $match;
    }

    /**
     * @return array|string
     */
    public function getAnswerByPrevMess(string $u_message)
    {
        $strictMatch = $this->getAnswer($u_message, 'next');
    }
}