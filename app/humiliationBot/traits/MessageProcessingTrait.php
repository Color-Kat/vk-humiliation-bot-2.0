<?php

namespace humiliationBot\traits;

use humiliationBot\ProcessingFunctions;

trait MessageProcessingTrait
{
    private ProcessingFunctions $processingFunctions;

    /**
     * processing message - vars substitution, select random part of proposal
     *
     * @param string $template string to variables substitution
     * @return string final message string
     */
    public function messageProcessing(string $template, array &$replaced_vars = []): string {
        $this->processingFunctions = new ProcessingFunctions();

        // substitute variables
        $message = $this->messageVarSubstitution($template);

        // replace rand() construction to one of many variants of phrase
        // example: rand(variant1, variant2) -> random -> variant1
        $message = $this->funcSubstitution($message);

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
    public function messageVarSubstitution(string $template, array &$replaced_vars = []): string {
        $message = $template;

        // search all (@var) and save it in $vars
        if (preg_match_all('/\(@\w+\)/u', $template, $vars)) {

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
                    $template
                );
            }
        }

        // return ready string message
        return $message;
    }

    public function funcSubstitution($template) {
        $message = $template;

        // get function calls - {@funcName(arg1|arg2|arg3)}
        $message = preg_replace_callback('/{@(?<func>\w+?)\((?<params>.*?)\)}/i', function($m){
            $params = explode('|', $m['params']); // get params as array
            $funcName = $m['func']; // get func name

            // call function by $funcName with $params from processingFunctions class
            if(method_exists($this->processingFunctions, $funcName))
                // and substitute the result of the function instead of calling it
                return $this->processingFunctions->$funcName($params);
            else return $params[0];
        }, $message);

        return $message;
    }
}