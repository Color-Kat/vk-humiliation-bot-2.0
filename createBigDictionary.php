<?php

$dictionariesDir = './app/humiliationBot/dictionaries';
$dirPathDictionaries = $dictionariesDir . '/parts/dictionaries';
$dirPathWordbooks = $dictionariesDir . '/parts/wordbooks';
$dictionaries = array_merge(scandir($dirPathDictionaries), scandir($dirPathWordbooks));

$bigDictionary = [];
$wordbook = [];
$with_prev_messages = [];

function get_with_prev_messages($answers)
{
    global $with_prev_messages;

    if (gettype($answers) != "string") {
        foreach ($answers as $answer) {
            if (is_array($answer)) {
                if(!isset($answer['messages'])) print_r($answer);
                get_with_prev_messages($answer['messages']);
            }

            if (isset($answer['with_prev_messages']) || isset($answer['with_prev_mess_id'])) {
                $with_prev_messages['id_'.$answer['with_prev_mess_id']] = $answer;
                get_with_prev_messages($answer['next']);
            }
        }
    }
}

// iterate all dictionaries and wordbooks
foreach ($dictionaries as $filename) {
    // skip . and .. directories
    if ($filename == '.' || $filename == '..') continue;

    // check filename to get folder (dictionaries/wordbooks)
    $dirPath = $dictionariesDir . '/parts';
    if(preg_match('/^d_/', $filename)) $dirPath .= '/dictionaries';
    elseif (preg_match('/^w_/', $filename)) $dirPath .= '/wordbooks';

    // get file content
    $file = json_decode(
        file_get_contents($dirPath . DIRECTORY_SEPARATOR . $filename),
        true);

    if (!isset($file['type'])){
        echo 'файл без type';
        continue;
    }

    switch ($file['type']) {
        // processing wordbook
        case 'wordbook':
            // get the name another wordbook that is included in this wordbook
            if(isset($file['use'])) {
                $uses = $file['use'];

                foreach ($uses as $use) {
                    // get file content of included wordbook
                    $usedWordbook = json_decode(file_get_contents(
                        $dirPathWordbooks . DIRECTORY_SEPARATOR . 'w_' . $use . '.json'
                    ), true);

                    $file['content'] += array_merge($file['content'], $usedWordbook['content']);
                }
            }

            // save file content to $wordbook
            $wordbook[$file['name']] = $file['content'];

            break;

        case 'dictionary':
            // save content to $bigDictionary
            $bigDictionary['name_' . $file['content']['name']] = $file['content'];

            // recursively save answers with property with_prev_message
            get_with_prev_messages($file['content']['answers']);

            break;
    }
}

//print_r($wordbook);

// and save dictionaries to files
file_put_contents(
    $dictionariesDir . DIRECTORY_SEPARATOR . 'wordbook.json',
    json_encode($wordbook, JSON_UNESCAPED_UNICODE)
);

file_put_contents(
    $dictionariesDir . DIRECTORY_SEPARATOR . 'bigDictionary.json',
    json_encode($bigDictionary, JSON_UNESCAPED_UNICODE)
);

file_put_contents(
    $dictionariesDir . DIRECTORY_SEPARATOR . 'with_prev_messages.json',
    json_encode($with_prev_messages, JSON_UNESCAPED_UNICODE)
);