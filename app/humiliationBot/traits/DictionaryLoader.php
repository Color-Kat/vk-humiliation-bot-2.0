<?php

namespace humiliationBot\traits;

// будет множество словарей, но, чтобы упростить работу, скриптом из контроллера,
// они будут складываться в один большой файл - bigDictionary.json,
// а также ответы с пометкой with_prev_messages будут складываться в файл with_prev_messages.json
// это позволит сначала просто проверить предыдущие сообщения, и, если нет совпадений, то только
// тогда отправлять другой ответ

/**
 * loading dictionaries, workbooks and other
 */
trait DictionaryLoader
{
    // methods to parse json files
    use JsonParser;

    /**
     * @var array current dictionary
     */
    protected array $dictionary = [];

    /**
     * @var array array of insults, praises, phrases and other parts of the proposal
     */
    protected array $wordbook = [];

    /**
     * @var array array of answers with property "$with_prev_messages"
     */
    protected array $with_prev_messages = [];

    /**
     * @var array list of sticker_ids [str_id => sticker_id]
     */
    protected array $stickerList = [];

    /**
     * load dictionary by name to property $dictionary
     *
     * @param string $name - name of dictionary
     * @return bool is success
     */
    public function loadDictionary(string $name): bool
    {
        $filename = DICTIONARY_PATH . '/bigDictionary.json'; // path to bigDictionary.json

        // get answer by name
        $dictionary = $this->setReaderPath($filename)->findByObjKey("name_$name");

        if(!$dictionary) return false;

        $this->dictionary = $dictionary;

        return true;
    }

    /**
     * load the whole wordbook to $wordbook
     *
     * @param array $additional array with additional variables
     * @return bool is success
     */
    public function loadWordbook(array $additional = []): bool
    {
        // wordbook is already loaded
        if(!empty($this->wordbook)) return true;

        $filename = DICTIONARY_PATH . '/wordbook.json';

        if(!file_exists($filename)) return false;

        // save dictionary by name
        $this->wordbook = json_decode(
            file_get_contents($filename),
            true
        );

        foreach ($additional as $var => $val) {
            $this->wordbook[$var] = $val;
        }

        return true;
    }

    /**
     * get answer with property "with_prev_messages" by with_prev_message_id
     *
     * @param string|false $with_prev_mess_id id of answer with property with_prev_messages
     * @return array|false answer
     */
    public function loadAnswerByPrevMessId($with_prev_mess_id)
    {
        // return a ready answer if this answer has already been found
        if (isset($this->with_prev_messages[$with_prev_mess_id]))
            return $this->with_prev_messages[$with_prev_mess_id];

        if(!$with_prev_mess_id) return false;

        $filename = DICTIONARY_PATH . '/with_prev_messages.json';

        // get answer by prev_mess_id
        $answer = $this->setReaderPath($filename)->findByObjKey("id_$with_prev_mess_id");

        // save to property
        $this->with_prev_messages[$with_prev_mess_id] = $answer;

        return $answer ?? false;
    }

    /**
     * load the stickers list ('str_id' => sticker_id)
     *
     * @return array|false list of sticker_ids
     */
    public function loadStickersList()
    {
        $filename = DICTIONARY_PATH . '/sticker_list.json';

        if(!file_exists($filename)) return false;

        // save dictionary by name
        return json_decode(
            file_get_contents($filename),
            true
        ) ?? false;
    }

}