<?php

namespace humiliationBot\entities;

use humiliationBot\traits\ExecFunctionsTrait;
use humiliationBot\traits\PatternProcessingTrait;

/**
 * class for work with ONLY array answers
 */
class AnswerObject
{
    // methods to processing answer pattern
    use PatternProcessingTrait;

    // methods that uses in dictionaries in "execFunc"
    use ExecFunctionsTrait;

    /**
     * @var array original answer array
     */
    private array $answerArrOriginal;

    /**
     * @var array array with variables to be substituted into a string
     */
    private array $wordbook = [];

    /**
     * @var string original answer type (string or array)
     */
    public string $answerType = "array";

    /**
     * @var string string with regex pattern to match
     */
    private string $pattern;

    /**
     * @var int priority of this answer (answer is taken from Answer with the highest priority)
     */
    private int $priority;

    /**
     * @var AnswerMessage|AnswerMessage[]|AnswerObject[] answer options
     */
    private $messages;

    /**
     * @var bool is need to work with next messages
     */
    private bool $with_prev_messages;

    /**
     * @var string id that allows to reply this message by answer from $next
     */
    private string $with_prev_mess_id;

    /**
     * @var string[] array of string with function names that we need to call
     */
    private array $execFunc;

    /**
     * @var array array with condition to check when answer matching
     */
    private array $conditions;

    /**
     * @var  AnswerMessage|AnswerMessage[]|AnswerObject[] answer option when replying to a prev message
     */
    private $next;

    public function __construct(array $answerArr, array $wordbook)
    {
        $this->answerArrOriginal = $answerArr; // set original answer array
        $this->wordbook = $wordbook; // set wordbook

        $this->initAnswerObj($answerArr);
    }

    private function initAnswerObj(array $answerArrOriginal)
    {
        $this->pattern = isset($answerArrOriginal['pattern'])
            ? $this->patternProcessing($answerArrOriginal['pattern'])
            : '';
        $this->priority = $answerArrOriginal['priority'] ?? 0;
        $this->messages = new Answers($answerArrOriginal, $this->wordbook);
        $this->with_prev_messages = $answerArrOriginal['with_prev_messages'] ?? false;
        $this->with_prev_mess_id = $answerArrOriginal['with_prev_mess_id'] ?? false;
        $this->execFunc = $answerArrOriginal['execFunc'] ?? [];
        $this->conditions = $answerArrOriginal['conditions'] ?? [];
    }

    /**
     * check does user's message strict match PATTERN this AnswerObject
     *
     * @param string $u_message user's message
     * @param ?int $minPriority minimal priority to match
     * @return array|false return false if it doesn't match, return answerArr if it matches
     */
    public function getMatch(string $u_message, ?int &$minPriority = 0, bool $isStrict = true)
    {
        if (!$this->pattern) {
            if ($isStrict) return false;
            else return $this->answerArrOriginal;
        }

        if (!$this->checkCondition()) return false; // return false if condition return false

        // processing pattern string (substitution wordbook vars, add flags)
        $pattern = $this->patternProcessing($this->pattern);

        // check match and
        // return original answer arr if priority is higher that minPriority
        if (preg_match($pattern, $u_message) && $this->priority >= $minPriority) {
            $minPriority = $this->priority; // update global priority
            return $this->answerArrOriginal;
        }

        return false; // don't match
    }

    /**
     * check condition for this AnswerObject
     *
     * @return bool return true if all conditions return true
     */
    public function checkCondition(): bool
    {
        // no condition - no problem
        if (!$this->conditions) return true;

        // IF ONLY ONE CONDITION RETURN FALSE
        // WE RETURN FALSE ;)

        $condition = $this->conditions;

        print_r($condition);

        // check is var set in wordbook
        if (isset($condition['isset'])) {
            foreach ((array) $condition['isset'] as $var) {
                if (!isset($this->wordbook[$var]) || !$this->wordbook[$var])
                    return false;
            }
        }

        // check is some variable is TRUE
        if (isset($condition['true'])) {
            foreach ((array) $condition['true'] as $var) {
                if (isset($this->wordbook[$var])){
                    if ($this->wordbook[$var] != true) return false;
                } else return false;
            }
        }

        // check is some variable is FALSE
        if (isset($condition['false'])) {
            foreach ((array) $condition['false'] as $var) {
                if (isset($this->wordbook[$var])){
                    if ($this->wordbook[$var] != false) return false;
                } else return false;
            }
        }

        // return true if every condition didn't return false
        return true;
    }
}