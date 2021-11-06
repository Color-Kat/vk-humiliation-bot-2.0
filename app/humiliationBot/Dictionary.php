<?php

namespace humiliationBot\traits;

class Dictionary
{
    // methods to load dictionaries
    use DictionaryLoader;

    public function getWordbook(): array
    {
        $isSuccess = $this->loadWordbook([
            'u_name' => $this->getName(),
            'u_name_gen' => $this->getName('gen'),
            'u_name_dat' => $this->getName('dat'),
            'u_name_acc' => $this->getName('acc'),
            'u_name_ins' => $this->getName('ins'),
            'u_name_abl' => $this->getName('abl'),
            'u_last_name' => $this->getLast_name(),
            'u_country' => $this->getCountry(),
            'u_city' => $this->getCity(),
            'u_birthday' => $this->getBirth(),
            'u_age' => $this->getAge(),
            'u_relation' => $this->getRelation(),
            'aliasName' => $this->getAliasName()
        ]);

        return $isSuccess ? $this->wordbook : false;
    }
}