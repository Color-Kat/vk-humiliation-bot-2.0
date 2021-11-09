<?php

class Photo
{
    private string $url;

    public function __construct($photo) {
        $maxSize = 0;
        foreach ($photo['sizes'] as $size){
            if($size['height'] > $maxSize) {
                $maxSize = $size['height'];
                $this->url = $size['url'];
            }
        }
    }

    public function getImageMessage(){

    }
}