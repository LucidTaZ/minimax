<?php

declare(strict_types=1);

namespace lucidtaz\minimax\engine;

use lucidtaz\minimax\game\GameState;
use lucidtaz\minimax\game\Player;

/**
 * Node in the decision search tree
 *
 * An object of this class can be queried for its ideal decision (and according
 * score) by calling the decide() method. It will recursively construct child
 * nodes and evaluate them using that method as well.
 */
class DecisionNode
{
    /**
     * @var Player The player to optimize for.
     */
    private $objectivePlayer;

    /**
     * @var GameState The current GameState to base future decisions on.
     */
    private $state;

    /**
     * @var int Limit on how deep we can continue to search, recursion limiter.
     */
    private $depthLeft;

    /**
     * @var NodeType Whether we are a min-node or a max-node. This enables the
     * caller to select either the most favorable or the least favorable
     * outcome.
     */
    private $type;

    /**
     * @var AlphaBeta Constraints for alpha-beta pruning
     */
    private $alphaBeta;

    /**
     * @param Player $objectivePlayer The Player to optimize for
     * @param GameState $state Current GameState to base decisions on
     * @param int $depthLeft Recursion limiter
     * @param NodeType $type Signifies whether to minimize or maximize the score
     * @param AlphaBeta $alphaBeta Range of potential scores to check
     */
    public function __construct(Player $objectivePlayer, GameState $state, int $depthLeft, NodeType $type, AlphaBeta $alphaBeta)
    {
        $this->objectivePlayer = $objectivePlayer;
        $this->state = $state;
        $this->depthLeft = $depthLeft;
        $this->type = $type;
        $this->alphaBeta = $alphaBeta;
    }

    /**
     * Determine the ideal move for this node
     *
     * This means either the best or the worst possible outcome for the
     * objective player, based on who is actually playing. (If the objective
     * player is currently playing, we take the best outcome, otherwise we take
     * the worst. This reflects that the opponent also plays optimally.)
     */
    public function traverseGameTree(): TraversalResult
    {
        if ($this->depthLeft === 0) {
            return TraversalResult::withoutMove($this->makeLeafEvaluation(), Analytics::forLeafNode());
        }

        $possibleMoves = $this->state->getPossibleMoves();
        if (empty($possibleMoves)) {
            return TraversalResult::withoutMove($this->makeLeafEvaluation(), Analytics::forLeafNode());
        }

        $analytics = Analytics::forInternalNode();
        $idealMove = null;
        $idealMoveResult = null;
        foreach ($possibleMoves as $move) {
            if (!$this->alphaBeta->isPositiveRange()) {
                // Subtree became fruitless, return to caller asap
                break;
            }

            $moveResult = $this->getChildResult($move);
            $analytics->add($moveResult->analytics);
            $this->alphaBeta->update($moveResult->evaluation, $this->type);
            if ($idealMoveResult === null || $this->isIdealOver($moveResult->evaluation, $idealMoveResult->evaluation)) {
                $idealMove = $move;
                $idealMoveResult = $moveResult;
            }
        }

        return TraversalResult::create($idealMove, $idealMoveResult->evaluation, $analytics);
    }

    /**
     * Formulate the evaluation, this node being a leaf node
     */
    private function makeLeafEvaluation(): Evaluation
    {
        $result = new Evaluation();
        $result->age = $this->depthLeft;
        $result->score = $this->state->evaluateScore($this->objectivePlayer);
        return $result;
    }

    /**
     * Recursively evaluate a child decision
     *
     * Apply a move and evaluate the outcome
     *
     * @param GameState $stateAfterMove The GameState that was created as a
     * result of a possible move.
     */
    private function getChildResult(GameState $stateAfterMove): TraversalResult
    {
        $nextPlayerIsFriendly = $stateAfterMove->getNextPlayer()->isFriendsWith($this->objectivePlayer);
        $nextDecisionPoint = new static(
            $this->objectivePlayer,
            $stateAfterMove,
            $this->depthLeft - 1,
            $nextPlayerIsFriendly ? NodeType::MAX() : NodeType::MIN(),
            clone $this->alphaBeta
        );
        return $nextDecisionPoint->traverseGameTree();
    }

    /**
     * Compare two evaluations
     *
     * The meaning of "best" is decided by the "ideal" member variable
     * comparator
     */
    private function isIdealOver(Evaluation $a, Evaluation $b): bool
    {
        $ideal = $this->type == NodeType::MIN()
            ? Evaluation::getWorstComparator()
            : Evaluation::getBestComparator();
        $idealEvaluationResult = $ideal($a, $b);
        return $idealEvaluationResult > 0;
    }
}
