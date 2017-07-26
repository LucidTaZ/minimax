<?php

namespace lucidtaz\minimax\engine;

use lucidtaz\minimax\game\GameState;

/**
 * An value type to hold an evaluation result and possibly a move
 * For tree traversal, technically only the evaluation result is needed.
 * However, at the root of the tree we need to know which move was selected to
 * produce that result, because that's the final answer that the caller is
 * interested in.
 */
class TraversalResult
{
    /**
     * @var ?GameState Empty if the traversed node was a leaf node
     */
    public $move;

    /**
     * @var Evaluation
     */
    public $evaluation;

    private function __construct(Evaluation $evaluation, GameState $move = null)
    {
        $this->move = $move;
        $this->evaluation = $evaluation;
    }

    public static function create(GameState $move, Evaluation $evaluation): TraversalResult
    {
        return new static($evaluation, $move);
    }

    public static function withoutMove(Evaluation $evaluation): TraversalResult
    {
        return new static($evaluation);
    }
}
