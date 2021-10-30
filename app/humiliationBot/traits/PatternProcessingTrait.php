<?php

namespace humiliationBot\traits;

trait PatternProcessingTrait
{
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
}