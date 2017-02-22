<?php

namespace lucidtaz\minimax\tests\tictactoe;

use lucidtaz\minimax\game\Decision as DecisionInterface;
use lucidtaz\minimax\game\GameState as GameStateInterface;

/**
 * TicTacToe game move
 *
 * This class represents the decision to place a symbol in the field specified
 * by its row and column numbers. By executing apply() on a TicTacToe GameState,
 * the new GameState will be returned where the symbol has been placed.
 *
 * Note that only the row and column are needed. The symbol is not specified
 * since TicTacToe has a fixed turn order, and the GameState already knows whose
 * turn it currently is.
 */
class Decision implements DecisionInterface
{
    private $row;
    private $column;

    public function __construct(int $row, int $column)
    {
        $this->row = $row;
        $this->column = $column;
    }

    public function apply(GameStateInterface $sourceState): GameStateInterface
    {
        $newState = clone $sourceState; // We must not change the $sourceState, the algorithm depends on that!
        $newState->makeMove($this->row, $this->column);
        return $newState;
    }
}
