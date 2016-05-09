<?php

namespace lucidtaz\minimax;

class DecisionWithScore
{
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
        if (abs($this->score - $other->score) < 0.1) {
            return $this->age > $other->age;
        }
        return $this->score > $other->score;
    }
}
