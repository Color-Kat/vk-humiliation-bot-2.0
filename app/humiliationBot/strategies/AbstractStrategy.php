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
     * get matches for user's message by pattern and return match with higher priority
     *
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

            // ----- substitute values from wordbook ----- //
            $pattern = $this->patternVarSubstitution($pattern);

            // add /ui flags to support russian language and case insensitivity
            $pattern .= 'ui';

            // check match and save answer with higher priority
            if (preg_match($pattern, $message) && ($answer['priority'] ?? 0) >= ($match['priority'] ?? 0)) {
                $match = $answer;
            }
        }

        return $match['messages'] ?? null;
    }

    /**
     * Substitute variables from $wordbook to $pattern
     *
     * @param $pattern
     * @return array|mixed|string|string[]|null
     */
    public function patternVarSubstitution($pattern) {
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

    /**
     * generate message by answers variants
     * with using getAnswerAlgorithm()
     *
     * @param array $messages answer variants
     * @return string ready message
     */
    public function generateMessage(array $messages): string{
        $answerTemplate = $this->getAnswerByAlgorithm($messages);

        $message = 'Бип-боп';

        if (gettype($answerTemplate) === "string") {
            return $this->messageVarSubstitution($answerTemplate);
        } elseif (gettype($answerTemplate) === "array") {
            Log::info('Нервный тип - array');
            return 'Array';
        }

        return 'Какой-то другой тип: ' .gettype($answerTemplate);
    }

    /**
     * Algorithm to get message from many variants
     * we can overload this methods in children to change this algorithm
     *
     * by default return random message
     *
     * @param array $messages
     * @return mixed
     */
    public function getAnswerByAlgorithm(array $messages){
        return $messages[array_rand($messages)];
    }

    /**
     * Substitute variables from $wordbook to $message
     *
     * @param string $message string to variables substitution
     * @param array $replaced_vars array with replaced variables (key: varName, value: varValue)
     * @return string final message string
     */
    public function messageVarSubstitution(string $message, array &$replaced_vars = []): string {
        // search all (@var) and save it in $vars
        if (preg_match_all('/\(@\w+\)/u', $message, $vars)) {

            // iterate over $vars and substitute RANDOM value from the wordbook
            foreach ($vars[0] as $var) {

                // get var name from (@var) and var value from wordbook
                $var = preg_replace('/[@\(\)]/', '', $var);
                $val = $this->wordbook[$var];

                // save into $replaced_vars
                $replaced_vars[$var] = $val;

                // substitute random value
                $message = preg_replace(
                    "/\(@$var\)/u",
                    $val[array_rand($val)],
                    $message
                );
            }
        }

        return $message;
    }

    /**
     * Generates one of many standard answer options from "standard" dictionary
     *
     * @param string $standardDictionaryName name of dictionary with standard message for this strategy
     * @return string
     */
    public function generateStandardAnswer(string $standardDictionaryName = 'standard'): string
    {
        // load dictionary with standards answers for this strategy
        $this->loadDictionary($standardDictionaryName);

        $message = $this->generateMessage($this->dictionary['answers']);

        return $message;
    }
}