<?php

namespace lucidtaz\minimax\tests\reversi;

use Generator;

/**
 * 8x8 Reversi board
 * It simply tracks which players own which cells.
 */
class Board
{
    /**
     * @var array Simple 8x8 array of Player objects to denote who owns it.
     */
    private $cells;

    public function __construct()
    {
        $this->initializeEmptyFields();
        $this->placeStartingTokens();
    }

    private function initializeEmptyFields()
    {
        $none = Player::NONE();
        for ($row = 0; $row < 8; $row++) {
            for ($column = 0; $column < 8; $column++) {
                $this->cells[$row][$column] = $none;
            }
        }
    }

    private function placeStartingTokens()
    {
        $this->cells[3][3] = Player::BLUE();
        $this->cells[3][4] = Player::RED();
        $this->cells[4][3] = Player::RED();
        $this->cells[4][4] = Player::BLUE();
    }

    public function isWithinBounds($row, $column): bool
    {
        return $row >= 0 && $row < 8 && $column >= 0 && $column < 8;
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

    public function countOwnedCells(Player $player): int
    {
        $ownedCells = 0;
        foreach ($this->cells as $rowValues) {
            foreach ($rowValues as $fieldValue) {
                if ($fieldValue->equals($player)) {
                    $ownedCells++;
                }
            }
        }
        return $ownedCells;
    }

    public function isOwnedBy(int $row, int $column, Player $player): bool
    {
        if (!$this->isWithinBounds($row, $column)) {
            return false;
        }

        $neighbordField = $this->getField($row, $column);
        return $neighbordField->equals($player);
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
}
