<?php

namespace humiliationBot;

class DictionaryFunctions
{
    public function rand(array $elements) {
        return $elements[array_rand($elements)];
    }

    public function repeat(array $elements): string {
        return str_repeat($elements[0], $elements[1]);
    }
}