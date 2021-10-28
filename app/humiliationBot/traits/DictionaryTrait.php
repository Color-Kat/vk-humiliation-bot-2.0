<?php

namespace humiliationBot\traits;

// будет множество словарей, но, чтобы упростить работу, скриптом из контроллера,
// они будут складываться в один большой файл - bigDictionary.json,
// а также ответы с пометкой with_prev_messages будут складываться в файл with_prev_messages.json
// это позволит сначала просто проверить предыдущие сообщения, и, если нет совпадений, то только
// тогда отправлять другой ответ

/**
 * work with dictionaries
 */
trait DictionaryTrait
{
    /**
     * @var array current dictionary
     */
    protected array $dictionary;
    protected array $with_prev_messages;

    /**
     * @param string $name - name of dictionary
     */
    public function loadDictionary(string $name): bool
    {
        // TODO сделать оптимизированный парсинг JSON

        // load bigDictionary.json (all dictionaries in one file)
        $bigDictionary = json_decode(
            file_get_contents(DICTIONARY_PATH . '/bigDictionary.json'
            ), true);

        if(!$bigDictionary[$name]) return false;

        // save dictionary by name
        $this->dictionary = $bigDictionary[$name];
        return true;
    }

    /**
     * @param string $with_prev_mess_id id of answer with property with_prev_messages
     * @return mixed answer
     */
    public function getPrevMessagesById(string $with_prev_mess_id) {
        // return a ready answer if this answer has already been found
        if(isset($this->with_prev_messages[$with_prev_mess_id]))
            return $this->with_prev_messages[$with_prev_mess_id];

        // get all answers with with_prev_messages
        $prevMessages = json_decode(
            file_get_contents(DICTIONARY_PATH . '/with_prev_messages.json'
            ), true);

        $answer = $prevMessages[$with_prev_mess_id] ?? false;

        // save answer
        $this->with_prev_messages[$with_prev_mess_id] = $answer;

        return $answer;
    }
}