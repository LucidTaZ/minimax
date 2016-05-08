<?php

namespace lucidtaz\minimax;

use BadMethodCallException;
use Closure;
use RuntimeException;

class Engine
{
    private $objectivePlayer;

    private $maxDepth;

    public function __construct(Player $objectivePlayer, int $maxDepth = 3)
    {
        $this->objectivePlayer = $objectivePlayer;
        $this->maxDepth = $maxDepth;
    }

    /**
     * Evaluate possible decisions and take the best one
     */
    public function decide(GameState $state): Decision
    {
        if (!$state->getNextPlayer()->equals($this->objectivePlayer)) {
            throw new BadMethodCallException('It is not this players turn');
        }
        $decisionWithScore = $this->decideMax($state, $this->maxDepth);
        if ($decisionWithScore->decision === null) {
            throw new RuntimeException('There are no possible moves');
        }
        return $decisionWithScore->decision;
    }

    private function decideMax(GameState $state, int $depthLeft)
    {
        return $this->decideVar($state, $depthLeft, function (DecisionWithScore $a, DecisionWithScore $b) {
            return $a->score > $b->score ? $a : $b;
        });
    }

    private function decideMin(GameState $state, int $depthLeft)
    {
        return $this->decideVar($state, $depthLeft, function (DecisionWithScore $a, DecisionWithScore $b) {
            return $a->score < $b->score ? $a : $b;
        });
    }

    private function decideVar(GameState $state, int $depthLeft, Closure $ideal)
    {
        if ($depthLeft == 0) {
            $result = new DecisionWithScore;
            $result->score = $state->evaluateScore($this->objectivePlayer);
            return $result;
        }

        /* @var $possibleMoves Decision[] */
        $possibleMoves = $state->getDecisions();
        if (empty($possibleMoves)) {
            $result = new DecisionWithScore;
            $result->score = $state->evaluateScore($this->objectivePlayer);
            return $result;
        }

        $bestDecisionWithScore = new DecisionWithScore;
        $bestDecisionWithScore->score = null; // TODO: Put score properly into a "case class", with UNKNOWN, MIN, MAX and Value(x)
        foreach ($possibleMoves as $move) {
            $newState = $move->apply($state);

            if ($newState->getNextPlayer()->isFriendsWith($this->objectivePlayer)) {
                $nextDecisionWithScore = $this->decideMax($newState, $depthLeft - 1);
            } else {
                $nextDecisionWithScore = $this->decideMin($newState, $depthLeft - 1);
            }

            if ($bestDecisionWithScore->score === null) {
                $bestDecisionWithScore = $nextDecisionWithScore;
                $bestDecisionWithScore->decision = $move;
            } else {
                $idealDecisionWithScoreFutureMoves = $ideal($nextDecisionWithScore, $bestDecisionWithScore);
                if ($idealDecisionWithScoreFutureMoves != $bestDecisionWithScore) {
                    $bestDecisionWithScore = $idealDecisionWithScoreFutureMoves;
                    $bestDecisionWithScore->decision = $move;
                }
            }
        }

        return $bestDecisionWithScore;
    }
}
