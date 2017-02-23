<?php

namespace lucidtaz\minimax\tests\tictactoe;

use lucidtaz\minimax\game\Decision as DecisionInterface;
use lucidtaz\minimax\game\GameState as GameStateInterface;
use lucidtaz\minimax\game\Player as PlayerInterface;

/**
 * Representation of the state of a TicTacToe game
 *
 * This is everything needed to know the game at a specific point in time,
 * namely:
 * - The contents of the board
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
     * @var Board Game board, keeping track of cell ownership.
     */
    public $board;

    /**
     * @var Player Reference to the player whose turn it currently is.
     */
    private $turn;

    public function __construct()
    {
        $this->board = new Board();
        $this->turn = Player::X();
    }

    public function __clone()
    {
        $this->board = clone $this->board;
        $this->turn = clone $this->turn;
    }

    /**
     * @param Decision $decision
     * @return GameState
     */
    public function applyDecision(DecisionInterface $decision): GameStateInterface
    {
        return $decision->apply($this);
    }

    public function makeMove(int $row, int $column)
    {
        $this->board->fillField($row, $column, $this->turn);
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

    /**
     * @param Player $player
     */
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
        if ($this->board->hasRow($player)) {
            return true;
        }
        if ($this->board->hasColumn($player)) {
            return true;
        }
        if ($this->board->hasDiagonal($player)) {
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
        foreach ($this->board->getEmptyFields() as $emptyField) {
            list($row, $col) = $emptyField;
            $decisions[] = new Decision($row, $col);
        }
        return $decisions;
    }

    public function getNextPlayer(): PlayerInterface
    {
        return $this->turn;
    }
}
