<?php

namespace humiliationBot\strategies;

use humiliationBot\entities\Answers;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class EditStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('message_edit');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        return isset($this->dictionary['answers'])
            ? ['messages' => $this->dictionary['answers']]
            : false;

//        return (new Answers($this->dictionary, $this->wordbook))
//            ->getAnswer($this->getMessage(), 'answers');
    }

    /**
     * @inheritdoc
     */
    public function getAnswerMessage($messages): string
    {
        if (!$messages) return $this->generateStandardAnswer();
        else return $this->generateAnswerMessage($messages);
    }
}