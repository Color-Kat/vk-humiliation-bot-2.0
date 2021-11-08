<?php

namespace humiliationBot;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;

/**
 * Паттерн стратегия (strategy)
 * Контекст
 */
class AnswerContext
{
    /**
     * @var VkMessageAnswerInterface ссылка на один из объектов стратегии,
     * работа с ним осуществляется через интерфейс стратегии
     */
    private VkMessageAnswerInterface $strategy;

    public function __construct(VkMessageAnswerInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    /**
     * set strategy to execute message answer logic
     */
    public function setStrategy(VkMessageAnswerInterface $strategy)
    {
        $this->strategy = $strategy;
    }

    public function answer(): bool
    {
        // get answer array by user's message, prev_mess_id and other
        $answerArr = $this->strategy->parse();

        // do other action
        if($answerArr) $this->strategy->doActions($answerArr);

        // generate message
        $message = $this->strategy->getAnswerMessage($answerArr['messages'] ?? false);

        $this->strategy->setMessage($message);
        $this->strategy->sticker($message);

        $this->strategy->sendMessage();

        return true;
    }
}