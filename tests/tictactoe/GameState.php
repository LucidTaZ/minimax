<?php

namespace lucidtaz\minimax\tests\tictactoe;

use lucidtaz\minimax\game\GameState as GameStateInterface;
use lucidtaz\minimax\game\Player as PlayerInterface;

/**
 * Representation of the state of a TicTacToe game
 *
 * This is everything needed to know the game at a specific point in time,
 * namely:
 * - The X-es and O-s on the board
 * - Whose turn it is
 *
 * From this it can calculate:
 * - Is there a win?
 * - Heuristic score (simply based on win = very high, loss = very low)
 * - What are the possible moves for the current player?
 */
class GameState implements GameStateInterface
{
    /**
     * @var array Simple 3x3 array of Player objects to denote who owns it.
     */
    private $board;

    /**
     * @var Player Reference to the player whose turn it currently is.
     */
    private $turn;

    public function __construct()
    {
        $none = Player::NONE();
        $this->board = [
            [$none, $none, $none],
            [$none, $none, $none],
            [$none, $none, $none],
        ];
        $this->turn = Player::X();
    }

    public function getField($row, $column): Player
    {
        return $this->board[$row][$column];
    }

    public function fillField(int $row, int $column)
    {
        $this->board[$row][$column] = $this->turn;
        $this->proceedPlayer();
    }

    private function proceedPlayer()
    {
        if ($this->turn->equals(Player::X())) {
            $this->turn = Player::O();
        } else {
            $this->turn = Player::X();
        }
    }

    public function evaluateScore(PlayerInterface $player): float
    {
        if ($this->win($player)) {
            return 999;
        }
        if ($this->lose($player)) {
            return -999;
        }
        return 0;
    }

    private function win(Player $player): bool
    {
        if ($this->hasRow($player)) {
            return true;
        }
        if ($this->hasColumn($player)) {
            return true;
        }
        if ($this->hasDiagonal($player)) {
            return true;
        }
        return false;
    }

    private function lose(Player $player): bool
    {
        if ($player->equals(Player::X())) {
            return $this->win(Player::O());
        }
        return $this->win(Player::X());
    }

    private function hasRow(Player $player): bool
    {
        foreach ($this->board as $row) {
            if ($player->equals($row[0]) && $player->equals($row[1]) && $player->equals($row[2])) {
                return true;
            }
        }
        return false;
    }

    private function hasColumn(Player $player): bool
    {
        for ($column = 0; $column < 3; $column++) {
            if ($player->equals($this->board[0][$column]) && $player->equals($this->board[1][$column]) && $player->equals($this->board[2][$column])) {
                return true;
            }
        }
        return false;
    }

    private function hasDiagonal(Player $player): bool
    {
        if ($player->equals($this->board[0][0]) && $player->equals($this->board[1][1]) && $player->equals($this->board[2][2])) {
            return true;
        }
        if ($player->equals($this->board[2][0]) && $player->equals($this->board[1][1]) && $player->equals($this->board[0][2])) {
            return true;
        }
        return false;
    }

    /**
     * Get all possible moves that can be taken by the current player
     * @return Decision[]
     */
    public function getDecisions(): array
    {
        if ($this->win($this->turn) || $this->lose($this->turn)) {
            return [];
        }
        $decisions = [];
        foreach ($this->board as $row => $rowValues) {
            foreach ($rowValues as $col => $fieldValue) {
                if ($fieldValue->equals(Player::NONE())) {
                    $decisions[] = new Decision($row, $col);
                }
            }
        }
        return $decisions;
    }

    public function getNextPlayer(): PlayerInterface
    {
        return $this->turn;
    }
}
