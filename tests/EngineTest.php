<?php

declare(strict_types=1);

namespace lucidtaz\minimax\tests;

use BadMethodCallException;
use LogicException;
use lucidtaz\minimax\engine\Engine;
use lucidtaz\minimax\tests\tictactoe\Board;
use lucidtaz\minimax\tests\tictactoe\GameState as TicTacToeGameState;
use lucidtaz\minimax\tests\tictactoe\Player;
use lucidtaz\minimax\tests\tictactoe\ShuffledDecisionsGameState;
use lucidtaz\minimax\tests\tictactoe\ZeroScoresGameState;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class EngineTest extends TestCase
{
    public function testEngineDecides(): void
    {
        $cleanState = new TicTacToeGameState();
        $engine = new Engine(Player::X());

        $this->assertEquals(0, $this->countFilledFields($cleanState->board), 'Test precondition');
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($cleanState);
        $this->assertEquals(1, $this->countFilledFields($newState->board));
    }

    public function testEngineDecidesWhenAllScoresZero(): void
    {
        // Regression test: it used to say "no possible moves".
        $cleanState = new ZeroScoresGameState();
        $engine = new Engine(Player::X());

        $this->assertEquals(0, $this->countFilledFields($cleanState->board), 'Test precondition');
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($cleanState);
        $this->assertEquals(1, $this->countFilledFields($newState->board));
    }

    private function countFilledFields(Board $board): int
    {
        $count = 0;
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($board->getField($row, $col) != Player::NONE()) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function testEngineTakesAWin1(): void
    {
        $X = Player::X();

        $state = new TicTacToeGameState();
        // XX
        //  O
        //   O
        $state->makeMove(0, 0); // X
        $state->makeMove(1, 1); // O
        $state->makeMove(0, 1); // X
        $state->makeMove(2, 2); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(0, 2)->equals($X), 'Upper-right field must be taken by X');
    }

    public function testEngineTakesAWin2(): void
    {
        // Rotated version of another test, to rule out accidental, unstrategic wins
        // If this test fails, it may be due to an alternative win in two turns,
        // instead of the intended win in one turn. This was fixed by preferring
        // solutions that were found earlier.
        $X = Player::X();

        $state = new TicTacToeGameState();
        // O
        //  O
        //  XX
        $state->makeMove(2, 2); // X
        $state->makeMove(1, 1); // O
        $state->makeMove(2, 1); // X
        $state->makeMove(0, 0); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(2, 0)->equals($X), 'Lower-left field must be taken by X');
    }

    public function testEnginePreventsALoss1(): void
    {
        $X = Player::X();

        $state = new TicTacToeGameState();
        // X
        // X
        // OO
        $state->makeMove(0, 0); // X
        $state->makeMove(2, 0); // O
        $state->makeMove(1, 0); // X
        $state->makeMove(2, 1); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(2, 2)->equals($X), 'Lower-right field must be taken by X');
    }

    public function testEnginePreventsALoss2(): void
    {
        // Rotated version of another test, to rule out accidental, unstrategic wins
        $X = Player::X();

        $state = new TicTacToeGameState();
        // OXX
        // O
        //
        $state->makeMove(0, 1); // X
        $state->makeMove(0, 0); // O
        $state->makeMove(0, 2); // X
        $state->makeMove(1, 0); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(2, 0)->equals($X), 'Lower-left field must be taken by X');
    }

    public function testEngineForcesAWin1(): void
    {
        $X = Player::X();

        $state = new TicTacToeGameState();
        // O X
        //  O
        // X
        $state->makeMove(2, 0); // X
        $state->makeMove(0, 0); // O
        $state->makeMove(0, 2); // X
        $state->makeMove(1, 1); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(2, 2)->equals($X), 'Bottom-right field must be taken by X');
    }

    public function testEngineForcesAWin2(): void
    {
        // Rotated version of another test, to rule out accidental, unstrategic wins
        $X = Player::X();

        $state = new TicTacToeGameState();
        // X O
        //  O
        //   X
        $state->makeMove(2, 2); // X
        $state->makeMove(0, 2); // O
        $state->makeMove(0, 0); // X
        $state->makeMove(1, 1); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(2, 0)->equals($X), 'Bottom-left field must be taken by X');
    }

    public function testEngineForcesAWin3(): void
    {
        // Randomly ordered decisions version of another test, to rule out accidental, unstrategic wins
        $X = Player::X();

        $state = new ShuffledDecisionsGameState();
        // X O
        //  O
        //   X
        $state->makeMove(2, 2); // X
        $state->makeMove(0, 2); // O
        $state->makeMove(0, 0); // X
        $state->makeMove(1, 1); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(2, 0)->equals($X), 'Bottom-left field must be taken by X');
    }

    public function testEnginePlaysAgainstItself(): void
    {
        // Tic Tac Toe Minimax against itself should always result in a draw
        $X = Player::X();
        $O = Player::O();

        $state = new TicTacToeGameState();

        // Note: This test (when confronted with a random shuffle in the
        // decision order) depends on high enough maxDepth values
        $engineX = new Engine($X, 6);
        $engineO = new Engine($O, 6);

        for ($i = 0; $i < 8; $i += 2) {
            $state = $engineX->decide($state);
            $state = $engineO->decide($state);
        }
        /** @var TicTacToeGameState $state */
        $state = $engineX->decide($state);

        $this->assertEquals(9, $this->countFilledFields($state->board), 'All fields must be full');
        $this->assertEquals(0, $state->evaluateScore($X), 'Player X must not win');
        $this->assertEquals(0, $state->evaluateScore($O), 'Player O must not win');
    }

    public function testEngineHandlesOneDrawOption(): void
    {
        $X = Player::X();

        $state = new TicTacToeGameState();
        // OXX
        //  OO
        // XOX
        $state->makeMove(2, 0); // X
        $state->makeMove(0, 0); // O
        $state->makeMove(0, 2); // X
        $state->makeMove(1, 1); // O
        $state->makeMove(0, 1); // X
        $state->makeMove(2, 1); // O
        $state->makeMove(2, 2); // X
        $state->makeMove(1, 2); // O

        $engine = new Engine($X);
        /** @var TicTacToeGameState $newState */
        $newState = $engine->decide($state);

        $this->assertTrue($newState->board->getField(1, 0)->equals($X), 'Middle-left field must be taken by X');
    }

    public function testExceptionWhenNotOurTurn(): void
    {
        $cleanState = new TicTacToeGameState();
        $engine = new Engine(Player::O());

        $this->expectException(LogicException::class);
        $engine->decide($cleanState);
    }

    public function testExceptionWhenNoMovesLeft(): void
    {
        $O = Player::O();

        $state = new TicTacToeGameState();
        // OXX
        // XOO
        // XOX
        $state->makeMove(2, 0); // X
        $state->makeMove(0, 0); // O
        $state->makeMove(0, 2); // X
        $state->makeMove(1, 1); // O
        $state->makeMove(0, 1); // X
        $state->makeMove(2, 1); // O
        $state->makeMove(2, 2); // X
        $state->makeMove(1, 2); // O
        $state->makeMove(1, 0); // X

        $engine = new Engine($O);

        $this->expectException(RuntimeException::class);
        $engine->decide($state);
    }

    public function testExceptionWhenInvalidMaxDepth(): void
    {
        $X = Player::X();

        $state = new TicTacToeGameState();
        $engine = new Engine($X, 0);

        $this->expectException(LogicException::class);
        $engine->decide($state);
    }

    public function testExceptionWhenGettingAnalyticsBeforeRunning(): void
    {
        $X = Player::X();

        $engine = new Engine($X, 0);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Please run decide() first.');

        $engine->getAnalytics();
    }
}
