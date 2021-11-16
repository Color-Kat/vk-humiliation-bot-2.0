<?php

namespace humiliationBot;

class DictionaryFunctions
{
    public function rand(array $elements) {
        return $elements[array_rand($elements)];
    }

    public function caps(string $mess) {
        return mb_strtoupper($mess);
    }

    public function repeat(array $elements): string {
        return str_repeat($elements[0], $elements[1]);
    }
}