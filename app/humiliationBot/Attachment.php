<?php

namespace humiliationBot;

use Photo;

class Attachment
{
    private string $type;

    public function __construct($attachment){
        $this->type = $attachment;
        echo 'it"s attachment';

        if($this->type == "photo") return new Photo($attachment[$this->type]);
    }
}