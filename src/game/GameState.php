<?php

namespace lucidtaz\minimax\game;

/**
 * Representation of the state of the game at any given moment
 */
interface GameState
{
    /**
     * Give possible Decisions (moves) from the current state
     * @return Decision[]
     */
    public function getDecisions(): array;

    /**
     * Apply the decided move and compute the resulting GameState
     *
     * Do not update the current game state, it is immutable! Note: this may be
     * relaxed by explicitly passing a clone from the engine, making the library
     * a bit friendlier.
     */
    public function applyDecision(Decision $decision): GameState;

    /**
     * Return the player that has its turn from here
     */
    public function getNextPlayer(): Player;

    /**
     * Return the score for the given Player
     *
     * This is required to determine which GameState is best when presented with
     * multiple options. Even if your game does not have scores, in this method
     * you must implement a heuristic that shows how favorable a GameState is.
     *
     * A higher number is favorable to a lower number. The meaning of the
     * numbers is free to choose (e.g. you can range the scores from 0..1, or
     * maybe have an unbounded, whole number point total.
     *
     * Scores can be lower than zero if desired.
     *
     * For absolute wins, return some very high constant, and for absolute
     * losses, return some very low constant.
     *
     * For example, in Tic Tac Toe you can return 999 if the given player has
     * won, -999 if the player has lost, and 0 for everything else.
     */
    public function evaluateScore(Player $player): float;
}
