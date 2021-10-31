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

    /**
     * @var array array of insults, praises, phrases and other parts of the proposal
     */
    protected array $wordbook;

    /**
     * @var array array of answers with property "$with_prev_messages"
     */
    protected array $with_prev_messages;

    /**
     * load dictionary by name to property $dictionary
     *
     * @param string $name - name of dictionary
     */
    public function loadDictionary(string $name): bool
    {
        // TODO сделать оптимизированный парсинг JSON

        $filename = DICTIONARY_PATH . '/bigDictionary.json';

        if(!file_exists($filename)) return false;

        // load bigDictionary.json (all dictionaries in one file)
        $bigDictionary = json_decode(
            file_get_contents($filename),
            true
        );

        if (!$bigDictionary[$name]) return false;

        // save dictionary by name
        $this->dictionary = $bigDictionary[$name];
        return true;
    }

    /**
     *
     */
    public function loadWordbook(): bool
    {
        $filename = DICTIONARY_PATH . '/wordbook.json';

        if(!file_exists($filename)) return false;

        // save dictionary by name
        $this->wordbook = json_decode(
            file_get_contents($filename),
            true
        );

        return true;
    }

    /**
     * get answer with property "with_prev_messages" by with_prev_message_id
     *
     * @param string $with_prev_mess_id id of answer with property with_prev_messages
     * @return mixed answer
     */
    public function getPrevMessagesById(string $with_prev_mess_id)
    {
        // return a ready answer if this answer has already been found
        if (isset($this->with_prev_messages[$with_prev_mess_id]))
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