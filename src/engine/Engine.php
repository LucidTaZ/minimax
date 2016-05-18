<?php

namespace lucidtaz\minimax\engine;

use BadMethodCallException;
use lucidtaz\minimax\game\Decision;
use lucidtaz\minimax\game\GameState;
use lucidtaz\minimax\game\Player;
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
        $topLevelNode = new DecisionPoint(
            $this->objectivePlayer,
            $state,
            $this->maxDepth,
            DecisionWithScore::getBestComparator()
        );
        $decisionWithScore = $topLevelNode->decide();
        if ($decisionWithScore->decision === null) {
            throw new RuntimeException('There are no possible moves');
        }
        return $decisionWithScore->decision;
    }
}