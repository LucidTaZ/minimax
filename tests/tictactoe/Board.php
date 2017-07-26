<?php

namespace lucidtaz\minimax\tests\tictactoe;

use Generator;

/**
 * 3x3 Tic-tac-toe board
 * It simply tracks which players own which cells.
 */
class Board
{
    /**
     * @var array Simple 3x3 array of Player objects to denote who owns it.
     */
    private $cells;

    public function __construct()
    {
        $none = Player::NONE();
        $this->cells = [
            [$none, $none, $none],
            [$none, $none, $none],
            [$none, $none, $none],
        ];
    }

    /**
     * Tells which player owns the specified field
     * Player::NONE() in case the field is not owned.
     */
    public function getField($row, $column): Player
    {
        return $this->cells[$row][$column];
    }

    public function fillField(int $row, int $column, Player $owner)
    {
        $this->cells[$row][$column] = $owner;
    }

    /**
     * Does the player own a whole row?
     */
    public function hasRow(Player $player): bool
    {
        foreach ($this->cells as $row) {
            if ($player->equals($row[0]) && $player->equals($row[1]) && $player->equals($row[2])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Does the player own a whole column?
     */
    public function hasColumn(Player $player): bool
    {
        for ($column = 0; $column < 3; $column++) {
            if ($player->equals($this->cells[0][$column])
                && $player->equals($this->cells[1][$column])
                && $player->equals($this->cells[2][$column])
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Does the player own either diagonal?
     */
    public function hasDiagonal(Player $player): bool
    {
        if ($player->equals($this->cells[0][0])
            && $player->equals($this->cells[1][1])
            && $player->equals($this->cells[2][2])
        ) {
            return true;
        }
        if ($player->equals($this->cells[2][0])
            && $player->equals($this->cells[1][1])
            && $player->equals($this->cells[0][2])
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return Generator Each element is an (x, y) tuple
     */
    public function getEmptyFields(): Generator
    {
        foreach ($this->cells as $row => $rowValues) {
            foreach ($rowValues as $col => $fieldValue) {
                if ($fieldValue->equals(Player::NONE())) {
                    yield [$row, $col];
                }
            }
        }
    }

    public function __toString(): string
    {
        return
            "{$this->cells[0][0]}{$this->cells[0][1]}{$this->cells[0][2]}\n" .
            "{$this->cells[1][0]}{$this->cells[1][1]}{$this->cells[1][2]}\n" .
            "{$this->cells[2][0]}{$this->cells[2][1]}{$this->cells[2][2]}\n";
    }
}
