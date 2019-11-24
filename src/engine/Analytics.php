<?php

declare(strict_types=1);

namespace lucidtaz\minimax\engine;

/**
 * Analytics holder
 *
 * This gives some insight in the evaluations done by the program.
 *
 * It is a result of tree traversal. Therefore this class knows how to aggregate
 * sub-results.
 */
class Analytics
{
    /**
     * @var int
     */
    public $nodesEvaluated;

    /**
     * @var int
     */
    public $leafNodesEvaluated;

    /**
     * @var int
     */
    public $internalNodesEvaluated;

    public static function forLeafNode(): Analytics
    {
        $result = new Analytics();
        $result->nodesEvaluated = 1;
        $result->leafNodesEvaluated = 1;
        $result->internalNodesEvaluated = 0;
        return $result;
    }

    public static function forInternalNode(): Analytics
    {
        $result = new Analytics();
        $result->nodesEvaluated = 1;
        $result->leafNodesEvaluated = 0;
        $result->internalNodesEvaluated = 1;
        return $result;
    }

    public function add(Analytics $that): void
    {
        $this->nodesEvaluated += $that->nodesEvaluated;
        $this->leafNodesEvaluated += $that->leafNodesEvaluated;
        $this->internalNodesEvaluated += $that->internalNodesEvaluated;
    }
}
