<?php

namespace humiliationBot\traits;

trait DictionaryTrait
{
    protected $dictionary;

    public function loadDictionary(string $name){
        $bigDictionary = json_decode(file_get_contents(DICTIONARY_PATH . '/bigDictionary.json'), true);
        $this->dictionary = $bigDictionary[$name]['name'];
    }
}