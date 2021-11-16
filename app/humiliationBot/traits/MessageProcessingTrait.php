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
        if ($attempts > 0 && preg_match('/\{@\w+\((.|\s)*\)\}/ui', $message)) {
            return $this->messageProcessing($message, $attempts - 1);
        }

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
        $re = '/{@(?<func>\w+)(\((?<params>(?:[^()]++|(?2))*)\))}/ui';
        $message = preg_replace_callback($re, function ($m) {
//        $message = preg_replace_callback('/{@(?<func>\w+?)\((?<params>.*)\)}/ui', function ($m) {
//            $params = explode('|', $m['params']); // get params as array
            $params = $this->getStrFuncParams($m['params']); // get params as array

            $funcName = $m['func']; // get func name

            // call function by $funcName with $params from processingFunctions class
            if (method_exists($this->processingFunctions, $funcName))
                // and substitute the result of the function instead of calling it
                return $this->processingFunctions->$funcName($params);
            else return $params[0];
        }, $message, 1);

        return $message;
    }

    private function getStrFuncParams(string $text): array{
//        return preg_split("~{@\w+\(.*?\)}(?![^|])(*SKIP)(*F)|\|~ui", $text);
        return preg_split("~{@\w+\(.*?\)}(*SKIP)(*F)|\|~ui", $text);
    }

    private function getStrFuncParams2(string $text): array{
        $params = [];

//        echo $text;

       // if in string is function set
        if (
            preg_match_all("/(@(\S+)\((.*)\))/", $text, $matches) &&
            isset($matches[0]) &&
            count($matches[0]) > 0
        ) {
            // in function ca
            $rtext = preg_replace("/\|/", "~", $matches[0][0]);
            //preg_quotes экранирует спец.символы использующиеся в регулярных выражениях - а их у нас в полученной строке полно
            $text = preg_replace("/".preg_quote($matches[0][0])."/", $rtext, $text);

            //После чего спокойно разбил всю исходную строку `$text`, по символу `|`
            $params = explode("|", $text);

            if ( is_array( $params ) && count( $params ) > 0 ) {
                //Далее идем по массиву и ищем элемент-строку начинающийся на '@'
                foreach( $params as $p ) {
                    $p = trim( $p );
                    if ( mb_substr( $p, 0, 1) == '@' ) {
                        //@rand(тест {@rand(номер 1234&номер 4321)})
                        //А теперь парсим эту строку на содержание параметров
                        //Если кол-во параметров другое или формат строки отличается - подправьте регулярку
                        if ( preg_match_all("/^\@\S+\(\W+\{\@\S+\((.+?)\&(.+?)\)\}\)/", $p, $matches) && is_array( $matches ) && count($matches) > 0 && is_array($matches[0]) && count($matches[0]) > 0) {


                            $params[] = $matches; //номер 1234
                        }
                    }
                }


            }

        } else {
            $params = explode('|', $text);
        }

        return $params;
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