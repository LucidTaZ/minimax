<?php

namespace lucidtaz\minimax\engine;

use Closure;
use lucidtaz\minimax\game\GameState;

/**
 * Value object containing a decision and its resulting score
 * These two concepts are used together so often that it warrants a separate
 * class to be able to carry them around easily.
 */
class DecisionWithScore
{
    const EPSILON = 0.00001;

    /**
     * @var GameState
     */
    public $decision = null;

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

    public function isBetterThan(DecisionWithScore $other): bool
    {
        if (abs($this->score - $other->score) < self::EPSILON) {
            // Scores are considered the same, prefer earliest decision. (Shallowest node)
            return $this->age > $other->age;
        }
        return $this->score > $other->score;
    }

    public static function getBestComparator(): Closure
    {
        return function (DecisionWithScore $a, DecisionWithScore $b) {
            return $a->isBetterThan($b) ? $a : $b;
        };
    }

    public static function getWorstComparator(): Closure
    {
        return function (DecisionWithScore $a, DecisionWithScore $b) {
            return $b->isBetterThan($a) ? $a : $b;
        };
    }
}
