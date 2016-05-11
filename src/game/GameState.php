<?php

namespace lucidtaz\minimax\game;

/**
 * Representation of the state of the game at any given moment
 */
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
     *
     * A higher number is favorable to a lower number. The meaning of the
     * numbers is free to choose (e.g. you can range the scores from 0..1, or
     * maybe have an unbounded, whole number point total.
     *
     * Scores can be lower than zero if desired.
     *
     * For absolute wins, return some very high constant, and for absolute
     * losses, return some very low constant.
     */
    public function evaluateScore(Player $player): float;
}
