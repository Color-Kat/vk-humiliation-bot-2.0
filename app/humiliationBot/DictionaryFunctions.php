<?php

namespace humiliationBot;

class DictionaryFunctions
{
    public function rand(array $elements)
    {
        return $elements[array_rand($elements)];
    }

    public function caps(string $mess)
    {
        return mb_strtoupper($mess);
    }

    public function repeat(array $elements): string
    {
        return str_repeat($elements[0], $elements[1]);
    }

    public function diminutive_name(array $elements): string
    {
        $name = $elements[0];
        $length = mb_strlen($name);
        $diminutive_name = 'кляп' . mb_substr($name, $length - 3, $length);
        // Саша - кляпаша
        // ира - кляпира
        // Александр - кляпндр
        // Соня - кляпоня
        // Иван - кляпван
        // Дима - кляпима
        // Егор - кляпор
        // Дмитрий - кляприй
        return $diminutive_name;
    }
}
