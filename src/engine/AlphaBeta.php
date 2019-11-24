<?php

declare(strict_types=1);

namespace lucidtaz\minimax\engine;

/**
 * Alpha and beta pair for use in alpha/beta pruning
 */
class AlphaBeta
{
    /**
     * @var float
     */
    public $alpha;

    /**
     * @var float
     */
    public $beta;

    public function __construct(float $alpha, float $beta)
    {
        $this->alpha = $alpha;
        $this->beta = $beta;
    }

    public static function initial(): AlphaBeta
    {
        return new static(-INF, INF);
    }

    /**
     * Update the constraint with new information
     */
    public function update(Evaluation $evaluation, NodeType $nodeType): void
    {
        if ($nodeType == NodeType::MAX()) {
            $this->alpha = max($this->alpha, $evaluation->score);
        } elseif ($nodeType == NodeType::MIN()) {
            $this->beta = min($this->beta, $evaluation->score);
        }
    }

    /**
     * Check whether the value ranges (alpha..inf and -inf..beta) still overlap
     * If not, the conclusion is that the game tree branch can be pruned.
     */
    public function isPositiveRange(): bool
    {
        return $this->alpha < $this->beta;
    }

    public function __toString(): string
    {
        return "($this->alpha, $this->beta)";
    }
}
