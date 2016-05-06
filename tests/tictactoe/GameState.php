<?php

namespace lucidtaz\minimax\tests\tictactoe;

use lucidtaz\minimax\GameState as GameStateInterface;
use lucidtaz\minimax\Player as PlayerInterface;

class GameState implements GameStateInterface
{
    private $board;

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
            return 0;
        }
        $score = 0;
        foreach ($this->board as $row) {
            foreach ($row as $field) {
                if ($field->equals($player)) {
                    $score++;
                }
            }
        }
        return $score;
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
