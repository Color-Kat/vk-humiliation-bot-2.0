<?php

namespace humiliationBot;

class DictionaryFunctions
{
    public function rand(array $elements) {
        return $elements[array_rand($elements)];
    }
}