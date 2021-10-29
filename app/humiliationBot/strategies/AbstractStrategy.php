<?php

namespace humiliationBot\strategies;

use app\lib\Log;
use humiliationBot\interfaces\VkMessageAnswerInterface;
use humiliationBot\traits\DictionaryTrait;
use humiliationBot\VkMessage;

class AbstractStrategy extends VkMessage
{
    // methods for work with dictionaries
    use DictionaryTrait;

    public function __construct($data)
    {
        // load wordbook with insults, praises, phrases and more
        $this->loadWordbook();

        parent::__construct($data);
    }

    /**
     * @param string $message string to check matching
     * @param array $dictionary dictionary
     * @return mixed|null found
     */
    public function getMatch(string $message, array $dictionary)
    {
        $match = null;

        // TODO проверять type

        // iterate over all answers and search pattern match
        foreach ($dictionary['answers'] as $answer) {

            $pattern = $answer['pattern'];

            $pattern = $this->varSubstitution($pattern);

            // ----- substitute values from wordbook ----- //
//            // search all (@var) and save it in $vars
//            if (preg_match_all('/\(@\w+\)/u', $pattern, $vars)) {
//                // iterate over $vars and substitute values from the wordbook
//                foreach ($vars[0] as $var) {
//                    // get var from (@var)
//                    $var = preg_replace('/[@\(\)]/', '', $var);
//
//                    // and finally substitute values
//                    $pattern = preg_replace(
//                        "/\(@$var\)/u",
//                        '(' . implode('|', $this->wordbook[$var]) . ')',
//                        $pattern
//                    );
//                }
//            }

            // add /ui flags to support russian language and case insensitivity
            $pattern .= 'ui';

            // check match and save answer with higher priority
            if (preg_match($pattern, $message) && ($answer['priority'] ?? 0) >= ($match['priority'] ?? 0)) {
                $match = $answer;
            }
        }

        return $match['messages'];
    }

    public function varSubstitution($pattern) {
        // search all (@var) and save it in $vars
        if (preg_match_all('/\(@\w+\)/u', $pattern, $vars)) {

            // iterate over $vars and substitute values from the wordbook
            foreach ($vars[0] as $var) {

                // get var from (@var)
                $var = preg_replace('/[@\(\)]/', '', $var);

                // and finally substitute values
                $pattern = preg_replace(
                    "/\(@$var\)/u",
                    '(' . implode('|', $this->wordbook[$var]) . ')',
                    $pattern
                );
            }
        }

        return $pattern;
    }
}