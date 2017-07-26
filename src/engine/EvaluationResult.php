<?php

namespace lucidtaz\minimax\engine;

use Closure;

/**
 * Value object containing the result of a decision tree node evaluation
 * These two concepts are used together so often that it warrants a separate
 * class to be able to carry them around easily.
 */
class EvaluationResult
{
    const EPSILON = 0.00001;

    /**
     * @var float Score that results from applying the decision
     */
    public $score;

    /**
     * @var integer How deep in the execution tree this result was found. Higher
     * means earlier. This is to prefer earlier solutions to later solutions
     * with the same score, which will make the AI not delay a win without
     * reason.
     */
    public $age;

    public function isBetterThan(EvaluationResult $other): bool
    {
        if (abs($this->score - $other->score) < self::EPSILON) {
            // Scores are considered the same, prefer earliest decision. (Shallowest node)
            return $this->age > $other->age;
        }
        return $this->score > $other->score;
    }

    public static function getBestComparator(): Closure
    {
        return function (EvaluationResult $a, EvaluationResult $b) {
            return $a->isBetterThan($b) ? 1 : -1;
        };
    }

    public static function getWorstComparator(): Closure
    {
        return function (EvaluationResult $a, EvaluationResult $b) {
            return $b->isBetterThan($a) ? 1 : -1;
        };
    }
}
