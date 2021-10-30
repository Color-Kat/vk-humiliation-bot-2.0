<?php

namespace humiliationBot\traits;

trait MessageProcessingTrait
{
    /**
     * processing message - vars substitution, select random part of proposal
     *
     * @param string $template string to variables substitution
     * @return string final message string
     */
    public function messageProcessing(string $template, array &$replaced_vars = []): string {
        $message = $this->messageVarSubstitution($template);

//        $message = $this->randomPhraseSubstitution($message);

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
}