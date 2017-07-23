<?php

namespace lucidtaz\minimax\engine;

use BadMethodCallException;
use lucidtaz\minimax\game\GameState;
use lucidtaz\minimax\game\Player;
use RuntimeException;

/**
 * MiniMax game engine
 *
 * Construct an object of this class, give it the player to optimize for, and
 * call decide() when it is time for the player to make a move, in order to get
 * the Decision that the engine has taken for the player.
 */
class Engine
{
    private $objectivePlayer;

    private $maxDepth;

    /**
     * @param Player $objectivePlayer The player to play as
     * @param int $maxDepth How far ahead should the engine look?
     */
    public function __construct(Player $objectivePlayer, int $maxDepth = 3)
    {
        $this->objectivePlayer = $objectivePlayer;
        $this->maxDepth = $maxDepth;
    }

    /**
     * Evaluate possible decisions and take the best one
     * @param GameState $state Current state of the game for which there needs
     * to be made a decision. This implicitly means that the objective player
     * currently must have its turn in the GameState.
     * @return GameState The state resulting after the engine made its decision.
     */
    public function decide(GameState $state): GameState
    {
        if (!$state->getNextPlayer()->equals($this->objectivePlayer)) {
            throw new BadMethodCallException('It is not this players turn');
        }
        $topLevelNode = new DecisionNode(
            $this->objectivePlayer,
            $state,
            $this->maxDepth,
            NodeType::MAX()
        );
        $decisionWithScore = $topLevelNode->decide();
        if ($decisionWithScore->decision === null) {
            throw new RuntimeException('There are no possible moves');
        }
        return $decisionWithScore->decision;
    }
}
