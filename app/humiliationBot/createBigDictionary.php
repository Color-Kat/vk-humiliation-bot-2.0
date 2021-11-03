<?php

//$dirPath = DICTIONARY_PATH . '/simple';
$dirPath = './dictionaries/simple';
$dictionaries = scandir($dirPath);

$bigDictionary = [];
$wordbook = [];
$with_prev_messages = [];

function get_with_prev_messages($answers, $d = 1)
{
    global $with_prev_messages;

    if (gettype($answers) != "string") {
        foreach ($answers as $answer) {
            if (is_array($answer)) {
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

    // get file content
    $file = json_decode(file_get_contents($dirPath . DIRECTORY_SEPARATOR . $filename), true);

    switch ($file['type']) {
        // processing wordbook
        case 'wordbook':
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
    './dictionaries' . DIRECTORY_SEPARATOR . 'wordbook.json',
    json_encode($wordbook, JSON_UNESCAPED_UNICODE)
);

file_put_contents(
    './dictionaries' . DIRECTORY_SEPARATOR . 'bigDictionary.json',
    json_encode($bigDictionary, JSON_UNESCAPED_UNICODE)
);

file_put_contents(
    './dictionaries' . DIRECTORY_SEPARATOR . 'with_prev_messages.json',
    json_encode($with_prev_messages, JSON_UNESCAPED_UNICODE)
);