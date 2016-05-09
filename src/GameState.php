<?php

namespace lucidtaz\minimax;

interface GameState
{
    /**
     * Give possible Decisions from here
     * @return Decision[]
     */
    public function getDecisions(): array;

    /**
     * Return the player that has its turn from here
     */
    public function getNextPlayer(): Player;

    /**
     * Return the score for the given Player
     */
    public function evaluateScore(Player $player): float;
}
