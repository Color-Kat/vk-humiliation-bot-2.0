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
        // get answer variants by user's message
        $answerVariants = $this->strategy->parse();

        // generate message
        $message = $this->strategy->generateAnswer($answerVariants);

        $this->strategy->setMessage($message);
        $this->strategy->getStickerId($message);

        $this->strategy->sendMessage();

        return true;
    }
}