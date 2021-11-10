<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\entities\Answers;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class DocStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('doc');

        parent::__construct($data);
    }

    /**
     * @inheritdoc
     */
    public function parse()
    {
        return [
            'messages' => $this->dictionary['answers']
        ];
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