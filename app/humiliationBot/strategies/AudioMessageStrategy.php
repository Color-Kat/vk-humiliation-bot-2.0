<?php

namespace humiliationBot\strategies;

use humiliationBot\entities\Answers;
use humiliationBot\interfaces\VkMessageAnswerInterface;

class AudioMessageStrategy extends AbstractStrategy implements VkMessageAnswerInterface
{
    public function __construct($data)
    {
        // load dictionary by name
        $this->loadDictionary('audio_message');

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