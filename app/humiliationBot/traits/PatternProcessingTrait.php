<?php

namespace humiliationBot\traits;

trait PatternProcessingTrait
{
    /**
     * substitute variables in pattern string by template
     *
     * @param string $patternTemplate template of pattern string
     * @return string final pattern string
     */
    public function patternProcessing(string $patternTemplate): string{
        $pattern = $this->patternVarSubstitution($patternTemplate);

        // add /ui flags to support russian language and case insensitivity
        $pattern .= 'ui';

        return $pattern;
    }

    /**
     * Substitute variables from $wordbook to $pattern
     *
     * @param string $pattern
     * @return string pattern with substituted variables
     */
    private function patternVarSubstitution(string $pattern): string {
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