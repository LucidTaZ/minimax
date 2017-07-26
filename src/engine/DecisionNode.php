<?php

namespace lucidtaz\minimax\engine;

use lucidtaz\minimax\game\GameState;
use lucidtaz\minimax\game\Player;

/**
 * Node in the decision search tree
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
     * @param Player $objectivePlayer The Player to optimize for
     * @param GameState $state Current GameState to base decisions on
     * @param int $depthLeft Recursion limiter
     * @param NodeType $type Signifies whether to minimize or maximize the score
     */
    public function __construct(Player $objectivePlayer, GameState $state, int $depthLeft, NodeType $type)
    {
        $this->objectivePlayer = $objectivePlayer;
        $this->state = $state;
        $this->depthLeft = $depthLeft;
        $this->type = $type;
    }

    /**
     * Determine the ideal move for this node
     * This means either the best or the worst possible outcome for the
     * objective player, based on who is actually playing. (If the objective
     * player is currently playing, we take the best outcome, otherwise we take
     * the worst. This reflects that the opponent also plays optimally.)
     */
    public function traverseGameTree(): TraversalResult
    {
        if ($this->depthLeft == 0) {
            return TraversalResult::onlyEvaluation($this->makeLeafResult());
        }

        /* @var $possibleMoves GameState[] */
        $possibleMoves = $this->state->getPossibleMoves();
        if (empty($possibleMoves)) {
            return TraversalResult::onlyEvaluation($this->makeLeafResult());
        }

        $idealResult = null;
        $idealMove = null;
        foreach ($possibleMoves as $move) {
            $moveResult = $this->getChildResult($move);

            if ($idealResult === null || $this->isIdealOver($moveResult, $idealResult)) {
                $idealResult = $moveResult;
                $idealMove = $move;
            }
        }

        return TraversalResult::create($idealMove, $idealResult);
    }

    /**
     * Formulate the evaluation result, this node being a leaf node
     */
    private function makeLeafResult(): EvaluationResult
    {
        $result = new EvaluationResult();
        $result->age = $this->depthLeft;
        $result->score = $this->state->evaluateScore($this->objectivePlayer);
        return $result;
    }

    /**
     * Recursively evaluate a child decision
     * Apply a move and evaluate the outcome
     * @param GameState $stateAfterMove The GameState that was created as a
     * result of a possible move.
     */
    private function getChildResult(GameState $stateAfterMove): EvaluationResult
    {
        $nextPlayerIsFriendly = $stateAfterMove->getNextPlayer()->isFriendsWith($this->objectivePlayer);
        $nextDecisionPoint = new static(
            $this->objectivePlayer,
            $stateAfterMove,
            $this->depthLeft - 1,
            $nextPlayerIsFriendly ? $this->type : $this->type->alternate()
        );
        return $nextDecisionPoint->traverseGameTree()->evaluation;
    }

    /**
     * Compare two evaluation results
     * The meaning of "best" is decided by the "ideal" member variable
     * comparator
     */
    private function isIdealOver(EvaluationResult $a, EvaluationResult $b): bool
    {
        $ideal = $this->type == NodeType::MIN()
            ? EvaluationResult::getWorstComparator()
            : EvaluationResult::getBestComparator();
        $idealEvaluationResult = $ideal($a, $b);
        return $idealEvaluationResult > 0;
    }
}
