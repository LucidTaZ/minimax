<?php

namespace lucidtaz\minimax\tests\tictactoe;

/**
 * TicTacToe state that gives the possible decisions in a random order
 *
 * This is to rule out any tests that accidentally succeed because of
 * coincidence.
 *
 * Note that this approach uses inheritance rather than applying the arguably
 * more sensible Decorator pattern. The reason for this is that the code
 * (currently, perhaps it will be solved) it not really strict with regards to
 * typing, and will call methods on the TicTacToe GameState class that are not
 * defined in the GameState interface, such as makeMove(). Until such issues are
 * resolved by an interface redesign, we must resort to inheritance.
 *
 * Furthermore, a decorated object may lose its decoration when passing out
 * references of itself to other code. This makes the pattern more hassle than
 * it's worth.
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
