<?php

namespace humiliationBot;

class ProcessingFunctions
{
    public function rand(array $elements) {
        return $elements[array_rand($elements)];
    }

    public function getAliasName(){
        // TODO получать кличку из БД
        return 'Фунтик';
    }
}