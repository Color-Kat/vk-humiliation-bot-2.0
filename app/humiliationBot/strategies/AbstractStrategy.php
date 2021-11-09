<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\entities\AnswerMessage;
use humiliationBot\entities\AnswerObject;
use humiliationBot\entities\Answers;
use humiliationBot\traits\DictionaryLoader;
use humiliationBot\traits\ExecFunctionsTrait;
use humiliationBot\traits\MessageGenerator;
use humiliationBot\traits\UserTrait;
use humiliationBot\VkMessage;

class AbstractStrategy extends VkMessage
{
    // trait for get user's data and User model instance
    use UserTrait;

    // methods for work with dictionaries
    use DictionaryLoader;

    // methods to generate answer from $answerArr
    use MessageGenerator;

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
            'u_name'       => $this->getName(),
            'u_name_gen'   => $this->getName('gen'),
            'u_name_dat'   => $this->getName('dat'),
            'u_name_acc'   => $this->getName('acc'),
            'u_name_ins'   => $this->getName('ins'),
            'u_name_abl'   => $this->getName('abl'),
            'u_last_name'  => $this->getLast_name(),
            'u_country'    => $this->getCountry(),
            'u_city'       => $this->getCity(),
            'u_birthday'   => $this->getBirth(),
            'u_age'        => $this->getAge(),
            'u_relation'   => $this->getRelation(),
            'aliasName'    => $this->getAliasName(),
            'isSubscribed' => $this->isSubscribed()
        ]);
    }

    /**
     * return answer array by chance or false
     *
     * @return array|false
     */
    public function chance(){
        // ===== by CHANCE ===== //
        $dictionaryChance = $this->getChanceAnswer();
        if ($dictionaryChance)
            return ['messages' => $dictionaryChance['answers']];
        return false;
    }

    /**
     * try to get answer marked as "change"
     * that is, it means that this answer can be selected with a certain chance
     *
     * @return array|false dictionary with "answers" or false
     */
    public function getChanceAnswer(){
        $chanceList = [1000000, 100, 50, 25, 0];

        // iterate chances
        foreach ($chanceList as $chance) {
            // by random check chance
            if(rand(0, $chance) == $chance) {
                // try to load dictionary by chance name
                $dict = $this->loadDictionary("chance_$chance", true);

                if ($dict) return $dict;
            }
        }

        return false;
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
        // update prev_mess_id in db if we have an answer with NEW "prev_mess_id"
        if (
            (isset($answerArr['with_prev_messages']) ||
                isset($answerArr['with_prev_mess_id'])) &&
            $answerArr['with_prev_mess_id'] != $this->getPrevMessageId()
        ) {
            $this->setPrevMessageId($answerArr['with_prev_mess_id']);
        } // reset prev_mess_id ONLY if is savePrevMessId not set
        else if (!isset($answerArr['doAction']['savePrevMessId'])) {
            // clear prev_mess_id
            $this->setPrevMessageId('');
        }
        // ================================ //

        // ========== ExecFunc ========== //
        $this->execFunc((array) ($answerArr['execFunc'] ?? []));
        // ============================== //
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

            $failedMessages = [
                'Бип-боп',
                '*Искрится*',
                'жжжжжжжжжж',
                'пип-пиип',
                '*Пищание*',
                'вииииииии-вшш'
            ];

            // failed to create message
            return $failedMessages[array_rand($failedMessages)];
        }
    }

    /**
     * try to get sticker_id from message string by template (sticker_shortName)
     *
     * @param string $message message
     */
    public function sticker(string $message): void
    {
        preg_match('/\(sticker_(?<str_id>\w+)\)/', $message, $match);

        if (!isset($match['str_id'])) return;

        $ids = $this->loadStickersList();

        $this->setSticker($ids[$match['str_id']] ?? false);
    }

    public function photo(string $message): void{
        $this->setPhoto();
    }
}