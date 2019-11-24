<?php

declare(strict_types=1);

namespace lucidtaz\minimax\tests\reversi;

use BadMethodCallException;
use Generator;
use lucidtaz\minimax\game\GameState as GameStateInterface;
use lucidtaz\minimax\game\Player as PlayerInterface;

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

    /**
     * @var bool Indicates that the previous player could not make a move.
     */
    private $lastPlayerPassed;

    public function __construct()
    {
        $this->board = new Board();
        $this->turn = Player::BLUE();
        $this->lastPlayerPassed = false;
    }

    public function __clone()
    {
        $this->board = clone $this->board;
        $this->turn = clone $this->turn;
    }

    public function makeMove(int $row, int $column): void
    {
        if (!$this->isLegalMove($row, $column)) {
            throw new BadMethodCallException('Illegal move');
        }

        $this->board->fillField($row, $column, $this->turn);

        $anchorPieces = $this->findAnchorPieces($row, $column);
        $this->capturePieces($row, $column, $anchorPieces);

        $this->lastPlayerPassed = false;
        $this->proceedPlayer();
    }

    public function pass(): void
    {
        if ($this->lastPlayerPassed) {
            throw new BadMethodCallException('Cannot pass after the previous player passed.');
        }
        // TODO: Only allow when there are no legal moves
        $this->lastPlayerPassed = true;
        $this->proceedPlayer();
    }

    private function capturePieces(int $moveRow, int $moveColumn, Generator $anchorPieces): void
    {
        foreach ($anchorPieces as [$anchorColumn, $anchorRow]) {
            $dY = $anchorRow <=> $moveRow;
            $dX = $anchorColumn <=> $moveColumn;

            for ($i = 1; $i < 8; $i++) {
                $captureFieldX = $moveColumn + $dX * $i;
                $captureFieldY = $moveRow + $dY * $i;
                if ($captureFieldX === $anchorColumn && $captureFieldY === $anchorRow) {
                    break;
                }
                $this->board->fillField($captureFieldY, $captureFieldX, $this->turn);
            }
        }
    }

    private function proceedPlayer(): void
    {
        if ($this->turn->equals(Player::BLUE())) {
            $this->turn = Player::RED();
        } else {
            $this->turn = Player::BLUE();
        }
    }

    /**
     * @param Player $player
     */
    public function evaluateScore(PlayerInterface $player): float
    {
        $opponent = $player->equals(Player::BLUE()) ? Player::RED() : Player::BLUE();
        return $this->board->countOwnedCells($player) - $this->board->countOwnedCells($opponent);
    }

    /**
     * @return GameState[]
     */
    public function getPossibleMoves(): array
    {
        $possibleMoves = [];
        foreach ($this->board->getEmptyFields() as [$row, $col]) {
            if ($this->isLegalMove($row, $col)) {
                $stateAfterMove = clone $this;
                $stateAfterMove->makeMove($row, $col);
                $possibleMoves[] = $stateAfterMove;
            }
        }
        if (empty($possibleMoves) && !$this->lastPlayerPassed) {
            $passingMove = clone $this;
            $passingMove->pass();
            $possibleMoves[] = $passingMove;
        }
        return $possibleMoves;
    }

    private function isLegalMove(int $row, int $column): bool
    {
        foreach ($this->findAnchorPieces($row, $column) as $anchorPiece) {
            // At least one anchor piece found
            return true;
        }
        return false;
    }

    private function findAnchorPieces(int $row, int $column): Generator
    {
        $opponent = $this->turn->equals(Player::BLUE()) ? Player::RED() : Player::BLUE();

        // Check all eight directions for an opponent piece
        foreach ($this->enumerateDirections() as [$directionX, $directionY]) {
            $inspectCellX = $column + $directionX;
            $inspectCellY = $row + $directionY;

            if (!$this->board->isOwnedBy($inspectCellY, $inspectCellX, $opponent)) {
                continue;
            }

            // Bordering an opponent, check in that direction for the anchor piece
            for ($i = 2; $i < 8; $i++) {
                $anchorCellX = $column + $directionX * $i;
                $anchorCellY = $row + $directionY * $i;

                if ($this->board->isOwnedBy($anchorCellY, $anchorCellX, $this->turn)) {
                    // We found our anchor piece, move becomes valid
                    yield [$anchorCellX, $anchorCellY];
                    break;
                } elseif (!$this->board->isOwnedBy($anchorCellY, $anchorCellX, $opponent)) {
                    // Inspected field is not occupied by us nor the opponent, so it's an empty field
                    break;
                }
            }
        }
    }

    private function enumerateDirections(): Generator
    {
        for ($directionX = -1; $directionX <= 1; $directionX++) {
            for ($directionY = -1; $directionY <= 1; $directionY++) {
                if ($directionX !== 0 || $directionY !== 0) {
                    yield [$directionX, $directionY];
                }
            }
        }
    }

    public function getNextPlayer(): PlayerInterface
    {
        return $this->turn;
    }
}
