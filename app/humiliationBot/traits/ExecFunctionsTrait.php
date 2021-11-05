<?php

namespace humiliationBot\traits;

trait ExecFunctionsTrait
{
    public function createAliasName(){
        $aliasName = 'Фунтик';

        // no wordbook - no alias name
        if (!isset($this->wordbook) || !isset($this->wordbook['aliasNames'])) {
            $this->setAliasName($aliasName);
            return;
        }

        $aliasList = $this->wordbook['aliasNames'];

        echo $aliasList[array_rand($aliasList)];

        $this->setAliasName($aliasList[array_rand($aliasList)]);
    }
}