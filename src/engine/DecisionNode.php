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
     * Determine the ideal decision for this node
     * This means either the best or the worst possible outcome for the
     * objective player, based on who is actually playing. (If the objective
     * player is currently playing, we take the best outcome, otherwise we take
     * the worst. This reflects that the opponent also plays optimally.)
     * @return DecisionWithScore
     */
    public function decide(): DecisionWithScore
    {
        if ($this->depthLeft == 0) {
            return $this->makeLeafResult();
        }

        /* @var $possibleMoves GameState[] */
        $possibleMoves = $this->state->getPossibleMoves();
        if (empty($possibleMoves)) {
            return $this->makeLeafResult();
        }

        $bestDecisionWithScore = null;
        foreach ($possibleMoves as $move) {
            $bestDecisionWithScore = $this->considerMove($move, $bestDecisionWithScore);
        }

        return $bestDecisionWithScore;
    }

    /**
     * Formulate the resulting decision, considering we do not look any further
     * The reason for not looking further can either be due to hitting the
     * recursion limit or because the game has actually concluded.
     * @return DecisionWithScore
     */
    private function makeLeafResult(): DecisionWithScore
    {
        $result = new DecisionWithScore;
        $result->age = $this->depthLeft;
        $result->score = $this->state->evaluateScore($this->objectivePlayer);
        return $result;
    }

    /**
     * Apply a move and evaluate the outcome
     * @param GameState $stateAfterMove The result of taking the move
     * @param DecisionWithScore $bestDecisionWithScoreSoFar Best result
     * encountered so far. TODO: Can probably be cleaned up by moving that logic
     * to the caller.
     * @return DecisionWithScore
     */
    private function considerMove(
        GameState $stateAfterMove,
        DecisionWithScore $bestDecisionWithScoreSoFar = null
    ): DecisionWithScore {
        $nextDecisionWithScore = $this->considerNextMove($stateAfterMove);

        $replaced = false;
        $bestDecisionWithScore = $this->replaceIfBetter(
            $nextDecisionWithScore,
            $bestDecisionWithScoreSoFar,
            $replaced
        );
        if ($replaced) {
            $bestDecisionWithScore->decision = $stateAfterMove;
        }

        return $bestDecisionWithScore;
    }

    /**
     * Recursively evaluate a child decision
     * @param GameState $stateAfterMove The GameState that was created as a
     * result of the current move.
     * @return DecisionWithScore
     */
    private function considerNextMove(GameState $stateAfterMove): DecisionWithScore
    {
        $nextPlayerIsFriendly = $stateAfterMove->getNextPlayer()->isFriendsWith($this->objectivePlayer);
        $nextDecisionPoint = new static(
            $this->objectivePlayer,
            $stateAfterMove,
            $this->depthLeft - 1,
            $nextPlayerIsFriendly ? $this->type : $this->type->alternate()
        );
        return $nextDecisionPoint->decide();
    }

    /**
     * Take the best of the two operands
     * The meaning of "best" is decided by the "ideal" member variable
     * comparator
     * @param DecisionWithScore $new
     * @param DecisionWithScore $current
     * @param bool $replaced Set to true if the second operand was better
     * @return DecisionWithScore
     */
    private function replaceIfBetter(
        DecisionWithScore $new,
        DecisionWithScore $current = null,
        &$replaced = false
    ): DecisionWithScore {
        if ($current === null) {
            $replaced = true;
            return $new;
        }

        $ideal = $this->type == NodeType::MIN()
            ? DecisionWithScore::getWorstComparator()
            : DecisionWithScore::getBestComparator();
        $idealDecisionWithScore = $ideal($new, $current);
        if ($idealDecisionWithScore === $new) {
            $replaced = true;
            return $new;
        }

        $replaced = false;
        return $current;
    }
}
