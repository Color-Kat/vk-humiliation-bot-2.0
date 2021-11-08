<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\entities\AnswerMessage;
use humiliationBot\entities\AnswerObject;
use humiliationBot\entities\Answers;
use humiliationBot\traits\DictionaryLoader;
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
    use DictionaryLoader;

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
            'u_name'      => $this->getName(),
            'u_name_gen'  => $this->getName('gen'),
            'u_name_dat'  => $this->getName('dat'),
            'u_name_acc'  => $this->getName('acc'),
            'u_name_ins'  => $this->getName('ins'),
            'u_name_abl'  => $this->getName('abl'),
            'u_last_name' => $this->getLast_name(),
            'u_country'   => $this->getCountry(),
            'u_city'      => $this->getCity(),
            'u_birthday'  => $this->getBirth(),
            'u_age'       => $this->getAge(),
            'u_relation'  => $this->getRelation(),
            'aliasName'   => $this->getAliasName()
        ]);
    }

    /**
     * get prev message id from DB and get answer by this id
     *
     * @return array|false
     */
    public function getAnswer_by_prev_mess_id()
    {
        $prevMessageId = $this->getPrevMessageId();
        return $this->loadAnswerByPrevMessId($prevMessageId);
    }

    /**
     * work with forced_left count
     *
     * @param string $action get, decrease, reset
     * @return int|void
     */
    public function forcedCounter(string $action)
    {
        switch ($action) {
            // return current forced_left count
            case "get":
                return $this->userData['forced_left'];

            // decrease forced_left count
            case "decrease":
                $this->decreaseForcedLeft();
                break;

            // reset forced_left count to default
            case "reset":
                $this->resetForcedLeft();
                break;
        }
    }

    /**
     * do other logic in answer ARRAY like:
     *  - execute logic from $answerArr["doAction"] if parser return "doActions"
     *  - update prev_mess_id in db
     *  - exec functions
     *
     * @param array $answerArr
     * @return void
     */
    public function doActions(array $answerArr)
    {
        // ======== PREV_MESS_ID ========== //
        // update prev_mess_id in db if we have an answer with "prev_mess_id"
        if (isset($answerArr['with_prev_messages']) || isset($answerArr['with_prev_mess_id'])) {
            $this->setPrevMessageId($answerArr['with_prev_mess_id']);
        } // reset prev_mess_id ONLY if is savePrevMessId not set
        else if (!isset($answerArr['doLogic']['savePrevMessId'])) {
            // clear prev_mess_id
            $this->setPrevMessageId('');
        }
        // ================================ //

        // ========== ExecFunc ========== //
        $this->execFunc($answerArr['execFunc'] ?? []);
        // ============================== //
    }

    /**
     * get matches for user's message by answer with prev_mess
     *
     * @param string $message user's message (string to check matching)
     * @param array $answerArr with_prev_messages answer assoc array
     * @return array|string|false answer variants
     */
//    public function getMatchByPrevMess(string $message, array $answerArr)
//    {
//        $match = $this->getMatch($message, $answerArr, 'next');
//
//        /**
//         * get "simple" answers from "next" field
//         * simple answer - answer without "pattern" field
//         *
//         * @param array $answersArr - array with "next" field
//         * @return array
//         */
//        function findSimpleAnswer(array $answersArr): array
//        {
//            $simpleAnswers = [];
//
//            foreach ($answersArr['next'] as $answer) {
//                if (!isset($answer['pattern'])) $simpleAnswers[] = $answer;
//            }
//
//            return $simpleAnswers;
//        }
//
//        // no match by pattern
//        if (!$match) {
//            // Если нет совпадения - возвращаем simpleAnswer
//            // Если нет этого, то возвращаем forced - сообщение на крайний случай
//            // если нет и forced, то удаляем из БД prev_mess
//            // Если forced отработал более трех раз, то возвращаем forced_end
//
//            // if no match we need to find answer without pattern - simple answer
//            $simpleAnswer = findSimpleAnswer($answerArr);
//
//            if ($simpleAnswer) {
//                // remove prev_mess_id from db
//                $this->setPrevMessageId('');
//
//                // execute function from field "execFunc"
//                if (isset($simpleAnswer['execFunc']))
//                    $this->execFunc($simpleAnswer['execFunc']);
//
//                return $simpleAnswer;
//            }
//
//            // goto "forced"
//            forced:
//
//            // return forced messages
//            if (isset($answerArr['forced']) && $this->userData['forced_left'] > 0) {
//                // return FORCED message if forced_left count is not over yet (usually 3 count)
//                // don't clear prev_message_id because we need to go here again
//
//                // go to next block if condition return false
//                if (
//                    isset($answerArr['forced']['condition']) &&
//                    $this->checkCondition($answerArr['forced']['condition'])
//                ) {
//                    echo 'goto forced_end;';
//                    goto forced_end;
//                }
//
//                // decrease forced_left to avoid looping
//                $this->decreaseForcedLeft();
//
//                // execute function from field "execFunc"
//                if (isset($answerArr['forced']['execFunc']))
//                    $this->execFunc($answerArr['forced']['execFunc']);
//
//                // return forced messages
//                return $answerArr['forced'];
//            } else {
//
//                // goto "forced_end"
//                forced_end:
//
//                // clear prev_message_id
//                $this->setPrevMessageId('');
//
//                // reset count forced_left
//                $this->resetForcedLeft();
//
//                // return $match (false) if condition return false
//                if (
//                    isset($answerArr['forced']['condition']) &&
//                    $this->checkCondition($answerArr['forced_end']['condition'])
//                ) {
//                    echo 'return $match;';
//                    return $match;
//                }
//
//                // execute function from field "execFunc"
//                if (isset($answerArr['forced_end']['execFunc']))
//                    $this->execFunc($answerArr['forced_end']['execFunc']);
//
//                // return forced_end if is it set
//                return $answerArr['forced_end'] ?? false;
//            }
//        }
//
//        return $match;
//    }

    /**
     * get matches for user's message by pattern and return match with higher priority
     *
     * @param string $message user's message (string to check matching)
     * @param array $dictionary dictionary
     * @param string $answersKey key for get answers
     * @return string|array|false answer variants
     */
//    public function getMatch(string $message, array $dictionary, string $answersKey = 'answers')
//    {
//        if (!$dictionary || !isset($dictionary[$answersKey])) return false;
//
//        $match = false;
//
//        // TODO проверять type
//
//        // no pattern - no match
//        if (gettype($dictionary[$answersKey]) == "string") {
//            // clear prev_message_id because we found answer without 'next'
//            $this->setPrevMessageId('');
//            return $dictionary[$answersKey];
//        }
//
//        // iterate over all answers and search pattern match
//        foreach ($dictionary[$answersKey] as $answer) {
//            // continue if is no pattern
//            if (!isset($answer['pattern'])) continue;
//            if (!$this->checkCondition($answer['condition'] ?? false)) {
//                echo 'continue;';
//                continue;
//            }
//
//            // add /ui flags to support russian language and case insensitivity
//            $pattern = $answer['pattern'] . 'ui';
//
//            // substitute values from wordbook
//            $pattern = $this->patternVarSubstitution($pattern);
//
//            // check match and save answer with higher priority
//            if (preg_match($pattern, $message) && ($answer['priority'] ?? 0) >= ($match['priority'] ?? 0)) {
//                $match = $answer;
//            }
//        }
//
//        // clear prev_message_id because we found answer without 'next'
//        if ($match) $this->setPrevMessageId('');
//
//        // save prev_mess_id if with_prev_messages is set
//        if (isset($match['with_prev_messages']) || isset($match['with_prev_mess_id']))
//            $this->setPrevMessageId($match['with_prev_mess_id']);
//
//        // execute function from field "execFunc"
//        if (isset($match['execFunc']))
//            $this->execFunc($match['execFunc']);
//
//        return $match['messages'] ?? false;
//    }

    /**
     * generate message by array $messages
     * with using getAnswerAlgorithm().
     * getAnswerAlgorithm() can be overloaded in strategy
     *
     * @param array $messages answer variants
     * @return string ready string message
     */
    public function generateAnswerMessage(array $messages): string
    {
        // try to generate message
        $message = $this->generateMessage($messages);

        if ($message) return $message; // message is created, return it
        else {
            // ===== failed to create message ===== //

            // try to create a new message if it was not possible to create this message
            // we have 10 attempts to create new answer
            // this is to avoid the loop
            for ($attempts = 10; $attempts > 0; $attempts--) {
                $message = $this->generateMessage($messages);
                if ($message) return $message;
            }

            // failed to create message
            return 'Бип-боп';
        }
    }

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

//        // save in DB with_prev_mess_id if is it set
//        if (
//            isset($answerArr['with_prev_messages']) ||
//            isset($answerArr['with_prev_mess_id'])
//        ) {
//            $this->setPrevMessageId($answerArr['with_prev_mess_id']);
//        }
//
//        // go to next block if condition return false
//        if (isset($answerArr['condition']) && $this->checkCondition($answerArr['condition'])) {
//            return 'Бип-боп';
//        }
//
//        // execute function from "execFunc"
//        if (isset($answerArr['execFunc']))
//            $this->execFunc($answerArr['execFunc']);
//
//        // return recursive generating string message ;)
//        return $this->generateMessage($answerArr['messages']);
    }

    /**
     * get sticker_id from message string by template (sticker_123)
     *
     * @param string $message message
     */
    public function getStickerId(string $message)
    {
        preg_match('/\(sticker_(?<str_id>\w+)\)/', $message, $match);

        if (!isset($match['str_id'])) return false;

        $ids = $this->loadStickersList();

        $this->setSticker($ids[$match['str_id']] ?? false);
    }

    /**
     * try to execute functions from array
     *
     * @param array $execList array with function names
     */
    public function execFunc(array $execList)
    {
        foreach ($execList as $funcName) {
            if (method_exists($this, $funcName))
                $this->$funcName();
        }
    }
}