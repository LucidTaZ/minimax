<?php

declare(strict_types=1);

namespace lucidtaz\minimax\engine;

use lucidtaz\minimax\game\GameState;

/**
 * An value type to hold an evaluation result and possibly a move
 *
 * For tree traversal, technically only the evaluation result is needed.
 * However, at the root of the tree we need to know which move was selected to
 * produce that result, because that's the final answer that the caller is
 * interested in.
 */
final class TraversalResult
{
    /**
     * @var GameState|null Empty if the traversed node was a leaf node
     */
    public $move;

    /**
     * @var Evaluation
     */
    public $evaluation;

    /**
     * @var Analytics
     */
    public $analytics;

    private function __construct(Evaluation $evaluation, Analytics $analytics, GameState $move = null)
    {
        $this->move = $move;
        $this->evaluation = $evaluation;
        $this->analytics = $analytics;
    }

    public static function create(GameState $move, Evaluation $evaluation, Analytics $analytics): TraversalResult
    {
        return new static($evaluation, $analytics, $move);
    }

    public static function withoutMove(Evaluation $evaluation, Analytics $analytics): TraversalResult
    {
        return new static($evaluation, $analytics);
    }
}
