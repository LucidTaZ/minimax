<?php

namespace lucidtaz\minimax\engine;

use Closure;
use lucidtaz\minimax\game\Decision;

class DecisionWithScore
{
    const EPSILON = 0.00001;

    /**
     * @var Decision
     */
    public $decision = null;

    /**
     * @var float
     */
    public $score;

    /**
     * @var integer How deep in the execution tree this result was found. Higher
     * means earlier. This is to prefer earlier solutions to later solutions
     * with the same score.
     */
    public $age;

    public function isBetterThan(DecisionWithScore $other): bool
    {
        if (abs($this->score - $other->score) < self::EPSILON) {
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
