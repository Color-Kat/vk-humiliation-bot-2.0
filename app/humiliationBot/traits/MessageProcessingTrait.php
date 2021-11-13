<?php

namespace humiliationBot\traits;

use humiliationBot\DictionaryFunctions;

trait MessageProcessingTrait
{
    private DictionaryFunctions $processingFunctions;

    /**
     * processing message - vars substitution, select random part of proposal
     *
     * @param string $template string to variables substitution
     * @return string final message string
     */
    public function messageProcessing(string $template, int $attempts = 10, array &$replaced_vars = []): string
    {
        $this->processingFunctions = new DictionaryFunctions();

        // substitute variables
        $message = $this->messageVarSubstitution($template);

        // replace rand() construction to one of many variants of phrase
        // example: rand(variant1, variant2) -> random -> variant1
        $message = $this->funcSubstitution($message);

        // exec functions recursively if there is a function in the string yet

//        preg_match('/\{@\w+\((.|\s)*\)\}/ui', $message, $m);
//        print_r($m);
//        if ($attempts > 0 && preg_match('/\{@\w+\((.|\s)*\)\}/ui', $message)) {
//            return $this->messageProcessing($message, $attempts - 1);
//        }

        // capital letters after a period, etc.
        $message = $this->autoRegister($message);

        // return ready string message
        return $message;
    }

    /**
     * Substitute variables from $wordbook to $message
     *
     * @param string $template string to variables substitution
     * @param array $replaced_vars array with replaced variables (key: varName, value: varValue)
     * @return string message string with substituted variables
     */
    private function messageVarSubstitution(string $template, array &$replaced_vars = []): string
    {
        $message = $template;

        // search all (@var) and save it in $vars
        if (preg_match_all('/\(@\w+\)/u', $template, $vars)) {

            // iterate over $vars and substitute RANDOM value from the wordbook
            foreach ($vars[0] as $var) {

                // get var name from (@var) and var value from wordbook
                $var = preg_replace('/[@\(\)]/', '', $var);
                $val = $this->wordbook[$var];

                // find random value if value is array
                if (gettype($val) == "array") $val = $val[array_rand($val)];

                // save into $replaced_vars
                $replaced_vars[$var] = $val;

                // substitute random value
                $message = preg_replace(
                    "/\(@$var\)/u",
                    $val,
                    $message,
                    1
                );
            }
        }

        // return ready string message
        return $message;
    }

    private function funcSubstitution($template)
    {
        $message = $template;

        // get function calls - {@funcName(arg1|arg2|arg3)}
        $message = preg_replace_callback('/{@(?<func>\w+?)\((?<params>.*?)\)}/ui', function ($m) {
            $params = explode('|', $m['params']); // get params as array
            $funcName = $m['func']; // get func name

            // call function by $funcName with $params from processingFunctions class
            if (method_exists($this->processingFunctions, $funcName))
                // and substitute the result of the function instead of calling it
                return $this->processingFunctions->$funcName($params);
            else return $params[0];
        }, $message, 1);

        return $message;
    }

    /**
     * capitalizes at the beginning of a sentence
     *
     * @param string $message
     * @return string
     */
    public function autoRegister(string $message): string{
        // remove space before ,
        $message = preg_replace('/(\s+,)/ui', ',' , $message);

        // remove , after ! and ?
        $message = preg_replace_callback('/(\?|\!|\)|\(),/ui', function ($m) {
            return trim($m[0], ',');
        }, $message);

        // search letters after .!?  and replace it to upper case
        $message = preg_replace_callback('/(\.|\!|\?)\s(?<first>\w?)/ui', function ($m) {
            return mb_strtoupper($m[0]);
        }, $message);

        // return message with first letter upper case
        return mb_ucfirst($message);
    }
}