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
            return $a->isBetterThan($b) ? $a : $b;
        });
    }

    private function decideMin(GameState $state, int $depthLeft)
    {
        return $this->decideVar($state, $depthLeft, function (DecisionWithScore $a, DecisionWithScore $b) {
            return $b->isBetterThan($a) ? $a : $b;
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

        $bestDecisionWithScore = null;
        foreach ($possibleMoves as $move) {
            $newState = $move->apply($state);

            $nextDecisionWithScore = $this->considerNextMove($newState, $depthLeft - 1);

            $replaced = false;
            $bestDecisionWithScore = $this->replaceIfBetter(
                $ideal,
                $nextDecisionWithScore,
                $bestDecisionWithScore,
                $replaced
            );
            if ($replaced) {
                $bestDecisionWithScore->decision = $move;
            }
        }

        return $bestDecisionWithScore;
    }

    private function considerNextMove(GameState $state, $depthLeft): DecisionWithScore
    {
        if ($state->getNextPlayer()->isFriendsWith($this->objectivePlayer)) {
            return $this->decideMax($state, $depthLeft);
        }
        return $nextDecisionWithScore = $this->decideMin($state, $depthLeft);
    }

    private function replaceIfBetter(Closure $ideal, DecisionWithScore $new, DecisionWithScore $current = null, &$replaced = false): DecisionWithScore
    {
        if ($current === null) {
            $replaced = true;
            return $new;
        }

        $idealDecisionWithScore = $ideal($new, $current);
        if ($idealDecisionWithScore === $new) {
            $replaced = true;
            return $new;
        }

        $replaced = false;
        return $current;
    }
}
