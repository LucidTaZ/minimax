<?php

namespace lucidtaz\minimax\engine;

use Closure;
use lucidtaz\minimax\game\Decision;
use lucidtaz\minimax\game\GameState;
use lucidtaz\minimax\game\Player;

class DecisionPoint
{
    private $objectivePlayer;
    private $state;
    private $depthLeft;
    private $ideal;

    /**
     * @param Player $objectivePlayer The Player to optimize for
     * @param GameState $state Current GameState to base decisions on
     * @param int $depthLeft Recursion limiter
     * @param Closure $ideal Function that takes two DecisionWithScore objects
     * and returns the ideal one. In some situations this is the best, in others
     * it is the worst.
     */
    public function __construct(Player $objectivePlayer, GameState $state, int $depthLeft, Closure $ideal)
    {
        $this->objectivePlayer = $objectivePlayer;
        $this->state = $state;
        $this->depthLeft = $depthLeft;
        $this->ideal = $ideal;
    }

    public function decide(): DecisionWithScore
    {
        if ($this->depthLeft == 0) {
            return $this->makeLeafResult();
        }

        /* @var $possibleMoves Decision[] */
        $possibleMoves = $this->state->getDecisions();
        if (empty($possibleMoves)) {
            return $this->makeLeafResult();
        }

        $bestDecisionWithScore = null;
        foreach ($possibleMoves as $move) {
            $bestDecisionWithScore = $this->considerMove($move, $bestDecisionWithScore);
        }

        return $bestDecisionWithScore;
    }

    private function makeLeafResult(): DecisionWithScore
    {
        $result = new DecisionWithScore;
        $result->age = $this->depthLeft;
        $result->score = $this->state->evaluateScore($this->objectivePlayer);
        return $result;
    }

    private function considerMove(Decision $move, DecisionWithScore $bestDecisionWithScoreSoFar = null): DecisionWithScore
    {
        $newState = $move->apply($this->state);

        $nextDecisionWithScore = $this->considerNextMove($newState);

        $replaced = false;
        $bestDecisionWithScore = $this->replaceIfBetter(
            $nextDecisionWithScore,
            $bestDecisionWithScoreSoFar,
            $replaced
        );
        if ($replaced) {
            $bestDecisionWithScore->decision = $move;
        }

        return $bestDecisionWithScore;
    }

    private function considerNextMove(GameState $newState): DecisionWithScore
    {
        $nextPlayerIsFriendly = $newState->getNextPlayer()->isFriendsWith($this->objectivePlayer);
        $comparator = $nextPlayerIsFriendly ? DecisionWithScore::getBestComparator() : DecisionWithScore::getWorstComparator();
        $nextDecisionPoint = new static($this->objectivePlayer, $newState, $this->depthLeft - 1, $comparator);
        return $nextDecisionPoint->decide();
    }

    private function replaceIfBetter(DecisionWithScore $new, DecisionWithScore $current = null, &$replaced = false): DecisionWithScore
    {
        if ($current === null) {
            $replaced = true;
            return $new;
        }

        $ideal = $this->ideal;
        $idealDecisionWithScore = $ideal($new, $current);
        if ($idealDecisionWithScore === $new) {
            $replaced = true;
            return $new;
        }

        $replaced = false;
        return $current;
    }
}
