<?php

namespace app\humiliationBot;

/**
 * Паттерн стратегия (strategy)
 */
class Answer
{
    /**
     * @var \VkMessageAnswerInterface ссылка на один из объектов стратегии,
     * работа с ним осуществляется через интерфейс стратегии
     */
    private \VkMessageAnswerInterface $strategy;

    public function __construct()
    {

    }
}