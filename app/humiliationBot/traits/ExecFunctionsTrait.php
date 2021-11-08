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

        // set random aliasName in db
        $this->setAliasName($aliasList[array_rand($aliasList)]);
    }
}