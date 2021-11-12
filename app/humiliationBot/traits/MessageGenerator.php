<?php

namespace humiliationBot\traits;

use humiliationBot\entities\AnswerMessage;
use humiliationBot\entities\AnswerObject;

trait MessageGenerator
{
    /**
     * try to generate message by array $messages
     * return false if can't
     *
     * @param array $messages answer variants
     * @return string|false ready message if success
     */
    public function generateMessage(array $messages)
    {
        // select one concrete answer array
        $answer = $this->getAnswerByAlgorithm($messages);

        if (gettype($answer) === "string") {
            return (new AnswerMessage($answer, $this->wordbook))->getMessage();
        } elseif (gettype($answer) === "array") {
            if (empty($answer)) return false;
            return $this->generateMessageFromAnswerArray($answer);
        }

        return false;
    }

    /**
     * Algorithm to get message from many variants $messages
     * we can overload this methods in children to change this algorithm
     *
     * by default return random message
     *
     * @param array $messages array of messages (array of strings and answerArrs)
     * @return string|array
     */
    public function getAnswerByAlgorithm(array $messages)
    {
        $answer = $messages[array_rand($messages)];

        // return string because answer is string and there is no condition in string
        if (gettype($answer) == "string") return $answer;

        // go to next block if condition return false
        if (!(new AnswerObject($answer, $this->wordbook))->checkCondition()) {
            return [];
        }

        return $answer;
    }

    /**
     * Generates one of many standard answer options from "standard" dictionary
     *
     * @param string $standardDictionaryName name of dictionary with standard message for this strategy
     * @return string
     */
    public function generateStandardAnswer(string $standardDictionaryName = 'standard'): string
    {
        $errorMessage = 'Гриша не придумал остроумного ответа, возможно произошла ошибка/проблемы с словарём';

        // load dictionary with standards answers for this strategy
        if (!$this->loadDictionary($standardDictionaryName))
            return $errorMessage; // show error message if no dictionary

        if (!isset($this->dictionary['answers']))
            return $errorMessage; // show error message if no answers

        return $this->generateAnswerMessage((array)$this->dictionary['answers']);
    }

    /**
     * recursively generate string message from answer ARRAY
     *  - check condition
     *  - do actions, save prev_mess_id
     *  - execute functions
     *
     * @param array $answerArr answer variants
     * @return string|false final string message if success
     */
    public function generateMessageFromAnswerArray(array $answerArr)
    {
        if (!(new AnswerObject($answerArr, $this->wordbook))->checkCondition())
            return false;

        // do actions
        $this->doActions($answerArr);

        // recursively generate message from messages of this answer
        return $this->generateMessage((array)$answerArr['messages']);
    }
}