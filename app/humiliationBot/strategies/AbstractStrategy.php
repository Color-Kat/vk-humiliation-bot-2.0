<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\traits\ExecFunctionsTrait;
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

    // methods that uses in dictionaries in "execFunc"
    use ExecFunctionsTrait;

    public function __construct($data)
    {
        parent::__construct($data);

        // load user's data
        $this->initUser($this->getUserId());

        // load wordbook with insults, praises, phrases and more
        // and add additional variables from code
        $this->loadWordbook([
            'u_name' => $this->getName(),
            'u_name_gen' => $this->getName('gen'),
            'u_name_dat' => $this->getName('dat'),
            'u_name_acc' => $this->getName('acc'),
            'u_name_ins' => $this->getName('ins'),
            'u_name_abl' => $this->getName('abl'),
            'u_last_name' => $this->getLast_name(),
            'u_country' => $this->getCountry(),
            'u_city' => $this->getCity(),
            'u_birthday' => $this->getBirth(),
            'u_age' => $this->getAge(),
            'u_relation' => $this->getRelation(),
            'aliasName' => $this->getAliasName()
        ]);
    }

    /**
     * get matches for user's message by answer with prev_mess
     *
     * @param string $message user's message (string to check matching)
     * @param array $answerArr with_prev_messages answer assoc array
     * @return array|string|false answer variants
     */
    public function getMatchByPrevMess(string $message, array $answerArr)
    {
        $match = $this->getMatch($message, $answerArr, 'next');

        /**
         * get "simple" answers from "next" field
         * simple answer - answer without "pattern" field
         *
         * @param array $answersArr - array with "next" field
         * @return array
         */
        function findSimpleAnswer(array $answersArr): array {
            $simpleAnswers = [];

            foreach ($answersArr['next'] as $answer) {
                if(!isset($answer['pattern'])) $simpleAnswers[] = $answer;
            }

            return $simpleAnswers;
        }

        // no match by pattern
        if(!$match) {
            // Если нет совпадения - возвращаем simpleAnswer
            // Если нет этого, то возвращаем forced - сообщение на крайний случай
            // если нет и forced, то удаляем из БД prev_mess
            // Если forced отработал более трех раз, то возвращаем forced_end

            // if no match we need to find answer without pattern - simple answer
            $simpleAnswer = findSimpleAnswer($answerArr);

            if($simpleAnswer) {
                // remove prev_mess_id from db
                $this->setPrevMessageId('');

                // execute function from field "execFunc"
                if(isset($simpleAnswer['execFunc']))
                    $this->execFunc($simpleAnswer['execFunc']);

                return $simpleAnswer;
            }

            // return forced messages
            if($answerArr['forced'] && $this->userData['forced_left'] > 0) {
                // return FORCED message if forced_left count is not over yet (usually 3 count)
                // don't clear prev_message_id because we need to go here again

                // decrease forced_left to avoid looping
                $this->decreaseForcedLeft();

                // execute function from field "execFunc"
                if(isset($answerArr['forced']['execFunc']))
                    $this->execFunc($answerArr['forced']['execFunc']);

                // return forced messages
                return $answerArr['forced'];
            } else {
                // clear prev_message_id
                $this->setPrevMessageId('');

                // reset count forced_left
                $this->resetForcedLeft();

                // execute function from field "execFunc"
                if(isset($answerArr['forced_end']['execFunc']))
                    $this->execFunc($answerArr['forced_end']['execFunc']);

                // return forced_end if is it set
                return $answerArr['forced_end'] ?? false;
            }
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
        if(gettype($dictionary[$answersKey]) == "string") {
            // clear prev_message_id because we found answer without 'next'
            $this->setPrevMessageId('');
            return $dictionary[$answersKey];
        }

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

        // clear prev_message_id because we found answer without 'next'
        if($match) $this->setPrevMessageId('');

        // save prev_mess_id if with_prev_messages is set
        if(isset($match['with_prev_messages']) || isset($match['with_prev_mess_id']))
            $this->setPrevMessageId($match['with_prev_mess_id']);

        // execute function from field "execFunc"
        if(isset($match['execFunc']))
            $this->execFunc($match['execFunc']);

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

    /**
     * process
     *
     * @param array $answerArr answer variants
     * @return string final string message
     */
    public function generateMessageFromAnswerArray(array $answerArr): string {
        // save in DB with_prev_mess_id if is it set
        if (isset($answerArr['with_prev_messages']) || isset($answerArr['with_prev_mess_id'])) {
            $this->setPrevMessageId($answerArr['with_prev_mess_id']);
        }

        // execute function from "execFunc"
        if(isset($answerArr['execFunc']))
            $this->execFunc($answerArr['execFunc']);

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

    /**
     * get sticker_id from message string by template (sticker_123)
     *
     * @param string $message message
     */
    public function getStickerId(string $message){
        preg_match('/\(sticker_(?<str_id>\w+)\)/', $message, $match);

        if(!isset($match['str_id'])) return false;

        $ids = $this->loadStickersList();

        $this->setSticker($ids[$match['str_id']] ?? false);
    }

    /**
     * try to execute frunctions from array
     *
     * @param array $execList array with function names
     */
    public function execFunc(array $execList){
        foreach ($execList as $funcName){
            if(method_exists($this, $funcName))
                $this->$funcName();
        }
    }
}