<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\VkMessage;

class AbstractStrategy extends VkMessage
{
    // methods for work with dictionaries
    use DictionaryTrait;

    public function __construct($data)
    {
        // load wordbook with insults, praises, phrases and more
        $this->loadWordbook();

        parent::__construct($data);
    }

    public function getMatch(string $message, array $dictionary)
    {
        foreach ($dictionary['answers'] as $answer) {

            $pattern = $answer['pattern'];

            $insults = ['дурак', 'дурашка', 'идиот'];

            $pattern = preg_replace(
                '/\(@insult\)/',
                '(' . implode('|', $this->wordbook['insult']) . ')',
                $pattern
            );

            Log::info('pattern', $pattern);
        }

        return 123;
    }
}