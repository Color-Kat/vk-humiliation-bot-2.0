<?php

namespace humiliationBot\traits;

use app\lib\Log;
use pcrov\JsonReader\Exception;
use pcrov\JsonReader\InputStream\IOException;
use pcrov\JsonReader\InvalidArgumentException;
use pcrov\JsonReader\JsonReader;

trait JsonParser
{
    /**
     * @var JsonReader jsonReader instance
     */
    private JsonReader $reader;

    /**
     * @var bool to check is success reading file
     */
    public $isReaderSuccess = true;

    /**
     * set path to json file
     *
     * @param string $path path to file
     * @return bool is succes
     */
    public function setReaderPath(string $path): self{
        // add reader
        $this->reader = new JsonReader();

        // open file
        try {
            $this->reader->open($path);

        } catch (IOException | InvalidArgumentException $e) {
            $this->isReaderSuccess = false;
        }

        return $this;
    }

    /**
     * @param $key - object key to be found
     * @return mixed
     */
    public function findByObjKey(string $key){
        if(!$this->isReaderSuccess) return false;

        try {
            $this->reader->read();
            $this->reader->read($key);
        } catch (Exception $e) {
            return false;
        }

        return $this->reader->value();
    }
}