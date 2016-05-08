<?php

namespace lucidtaz\minimax\tests\tictactoe;

/**
 * TicTacToe state that gives the possible decisions in a random order
 *
 * This is to rule out any tests that accidentally succeed because of
 * coincidence.
 */
class ShuffledDecisionsGameState extends GameState
{
    public function getDecisions(): array
    {
        $decisions = parent::getDecisions();
        shuffle($decisions);
        return $decisions;
    }
}
