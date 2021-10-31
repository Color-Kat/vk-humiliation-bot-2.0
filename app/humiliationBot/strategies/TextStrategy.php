<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class TextStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{

    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('simple');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        // TODO get prev message from DB
        // get prev message if from DB and get answer by this id
        $prevMessageIdFromDB = 'little_train123';
        $messageByPrevMessage = $this->getPrevMessagesById($prevMessageIdFromDB);

        // return this answers
        if ($messageByPrevMessage) return $messageByPrevMessage['messages'];

        $match = $this->getMatch($this->getMessage(), $this->dictionary);

//        Log::info('isSub', $this->isSubscribed($this->getUserId()));
//        Log::info('match', $match);

        return $match;
    }

    /**
     * @inheritdoc
     */
    public function generateAnswer($messages): string
    {
        if($messages == null) return $this->generateStandardAnswer();
        else return $this->generateMessage($messages);
    }
}