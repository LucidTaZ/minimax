<?php

namespace lucidtaz\minimax;

class DecisionWithScore {
    /**
     * @var Decision
     */
    public $decision = null;

    /**
     * @var float
     */
    public $score;

    public function isBetterThan(DecisionWithScore $other): bool
    {
        return $this->score > $other->score;
    }
}
