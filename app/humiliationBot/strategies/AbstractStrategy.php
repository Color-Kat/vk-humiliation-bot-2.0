<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\traits\MessageProcessingTrait;
use humiliationBot\traits\PatternProcessingTrait;
use humiliationBot\traits\UserTrait;
use humiliationBot\VkMessage;

class AbstractStrategy extends VkMessage
{
    // trait for get user's data and User model instance
    use UserTrait;

    // methods for work with dictionaries
    use DictionaryTrait;

    // methods for processing pattern
    use PatternProcessingTrait;

    // methods for processing messages
    use MessageProcessingTrait;

    public function __construct($data)
    {
        parent::__construct($data);

        // load wordbook with insults, praises, phrases and more
        $this->loadWordbook();

        // load user's data
        $this->initUser($this->getUserId());
    }

    /**
     * get matches for user's message by answer with prev_mess
     *
     * @param string $message user's message (string to check matching)
     * @param array $answerObj with_prev_messages answer object
     * @return array|string|false answer variants
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

            if($simpleAnswer) {
                $this->setPrevMessageId(''); // remove prev_mess_id from db
                return $simpleAnswer;
            }

//            if($answerObj['forced']) return $answerObj['forced'];

            // TODO удалять из БД prev_mess
            // Если нет совпадения - возвращаем simpleAnswer
            // Если нет этого, то возвращаем forced - сообщение на крайний случай
            // если нет и forced, то удаляем из БД prev_mess
            // Если forced отработал более трех раз, то возвращаем forced_end
            return ($simpleAnswer ?? $answerObj['forced']) ?? false;
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
        if(!$dictionary || !isset($dictionary[$answersKey])) return false;

        $match = false;

        // TODO проверять type

        // no pattern - no match
        if(gettype($dictionary[$answersKey]) == "string") return $dictionary[$answersKey];

        // iterate over all answers and search pattern match
        foreach ($dictionary[$answersKey] as $answer) {
            // continue if is no pattern
            if(!isset($answer['pattern'])) continue;

            // add /ui flags to support russian language and case insensitivity
            $pattern = $answer['pattern'] . 'ui';

            // substitute values from wordbook
            $pattern = $this->patternVarSubstitution($pattern);

            // check match and save answer with higher priority
            if (preg_match($pattern, $message) && ($answer['priority'] ?? 0) >= ($match['priority'] ?? 0)) {
                $match = $answer;
            }
        }

        return $match['messages'] ?? false;
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
        print_r($answerArr);
        if ($answerArr['with_prev_messages'] || $answerArr['with_prev_mess_id']) {
            $this->setPrevMessageId($answerArr['with_prev_mess_id']);
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