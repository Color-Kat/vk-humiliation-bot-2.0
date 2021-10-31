<?php

//$dirPath = DICTIONARY_PATH . '/simple';
$dirPath = './dictionaries/simple';
$dictionaries = scandir($dirPath);

$bigDictionary = [];
$wordbook = [];
$with_prev_messages = [];

foreach ($dictionaries as $filename) {
    if($filename == '.' || $filename == '..') continue;

    $file = json_decode(file_get_contents($dirPath . DIRECTORY_SEPARATOR . $filename), true);

    switch ($file['type']){
        case 'wordbook':
            $wordbook[$file['name']] = $file['content'];

            break;
    }
}

//print_r($wordbook);

file_put_contents(
    './dictionaries' . DIRECTORY_SEPARATOR . 'wordbook.json',
    json_encode($wordbook, JSON_UNESCAPED_UNICODE )
);