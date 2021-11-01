<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\traits\MessageProcessingTrait;
use humiliationBot\traits\PatternProcessingTrait;
use humiliationBot\VkMessage;

class AbstractStrategy extends VkMessage
{
    // methods for work with dictionaries
    use DictionaryTrait;

    // methods for processing pattern
    use PatternProcessingTrait;

    // methods for processing messages
    use MessageProcessingTrait;

    public function __construct($data)
    {
        // load wordbook with insults, praises, phrases and more
        $this->loadWordbook();

        parent::__construct($data);
    }

    /**
     * get matches for user's message by answer with prev_mess
     *
     * @param string $message user's message (string to check matching)
     * @param array $answerObj with_prev_messages answer object
     * @return array|string answer variants
     */
    public function getMatchByPrevMess(string $message, array $answerObj)
    {
        $match = $this->getMatch($message, $answerObj, 'next');

        function findSimpleAnswer(array $answers): array {
            $simpleAnswers = [];

            foreach ($answers['next'] as $answer) {
                if(!isset($answer['pattern'])) $simpleAnswers[] = $answer;
            }

            return $simpleAnswers;
        }

        if(!$match) {
            // if no match we need to find answer without pattern - simple answer
            $simpleAnswer = findSimpleAnswer($answerObj);

            return $simpleAnswer ?? $answerObj['forced'];
        }

        return $match;
    }

    /**
     * get matches for user's message by pattern and return match with higher priority
     *
     * @param string $message user's message (string to check matching)
     * @param array $dictionary dictionary
     * @param string $answersKey key for get answers
     * @return string|array|false answer variants
     */
    public function getMatch(string $message, array $dictionary, string $answersKey = 'answers')
    {
        $match = false;

        // TODO проверять type

        if(gettype($dictionary[$answersKey]) == "string") return $dictionary[$answersKey];

        // iterate over all answers and search pattern match
        foreach ($dictionary[$answersKey] as $answer) {

            if(!isset($answer['pattern'])) continue;

            $pattern = $answer['pattern'];

            // ----- substitute values from wordbook ----- //
            $pattern = $this->patternVarSubstitution($pattern);

            // add /ui flags to support russian language and case insensitivity
            $pattern .= 'ui';

            // check match and save answer with higher priority
            if (preg_match($pattern, $message) && ($answer['priority'] ?? 0) >= ($match['priority'] ?? 0)) {
                $match = $answer;
            }
        }

        return $match['messages'] ?? null;
    }

    /**
     * generate message by answers variants
     * with using getAnswerAlgorithm()
     *
     * @param array|string $messages answer variants
     * @return string ready message
     */
    public function generateMessage($messages): string{
        $answerTemplate = $this->getAnswerByAlgorithm($messages);

        if (gettype($answerTemplate) === "string") {
            return $this->messageProcessing($answerTemplate);
        } elseif (gettype($answerTemplate) === "array") {
            return $this->generateMessageFromAnswerArray($answerTemplate);
        }

        return 'Бип-боп';
    }

    /**
     * Algorithm to get message from many variants $messages
     * we can overload this methods in children to change this algorithm
     *
     * by default return random message
     *
     * @param string|array $messages
     * @return mixed
     */
    public function getAnswerByAlgorithm($messages){
        if(gettype($messages) == "string") return $messages;
        return $messages[array_rand($messages)];
    }

    public function generateMessageFromAnswerArray(array $answerArr): string {
        if ($answerArr['with_prev_messages'] | $answerArr['with_prev_mess_id']) {
            // TODO save $answerArr['with_prev_mess_id'] to db
        }

        // return recursive generating string message ;)
        return $this->generateMessage($answerArr['messages']);
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
        if(!$this->loadDictionary($standardDictionaryName))
            return $errorMessage; // show error message if no dictionary

        if(!isset($this->dictionary['answers']))
            return $errorMessage; // show error message if no answers

        return $this->generateMessage($this->dictionary['answers']);
    }
}