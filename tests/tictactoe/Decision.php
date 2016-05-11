<?php

namespace lucidtaz\minimax\tests\tictactoe;

use lucidtaz\minimax\game\Decision as DecisionInterface;
use lucidtaz\minimax\game\GameState as GameStateInterface;

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
        $newState = clone $sourceState;
        $newState->fillField($this->row, $this->column);
        return $newState;
    }
}
