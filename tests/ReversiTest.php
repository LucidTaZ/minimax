<?php

namespace lucidtaz\minimax\tests;

use BadMethodCallException;
use lucidtaz\minimax\engine\Engine;
use lucidtaz\minimax\tests\reversi\GameState;
use lucidtaz\minimax\tests\reversi\Player;
use PHPUnit\Framework\TestCase;

/**
 * Tests the engine using Reversi
 *
 * This is a larger problem space than Tic-Tac-Toe. These tests cover the
 * performance aspect of the Minimax Engine.
 */
class ReversiTest extends TestCase
{
    public function testReversiDetectsIllegalMovesNoNeighbors()
    {
        $state = new GameState;

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Illegal move');
        $state->makeMove(0, 0);
    }

    public function testReversiDetectsIllegalMovesOwnNeighbor()
    {
        $state = new GameState;

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Illegal move');
        $state->makeMove(2, 3);
    }

    public function testReversiAllowsLegalMove()
    {
        $BLUE = Player::BLUE();
        $RED = Player::RED();

        $state = new GameState();
        $this->assertEquals(2, $state->board->countOwnedCells($BLUE), 'Test precondition');
        $this->assertEquals(2, $state->board->countOwnedCells($RED), 'Test precondition');
        $this->assertEquals($BLUE, $state->getNextPlayer(), 'Test precondition');

        $newState = clone $state;
        $newState->makeMove(2, 4);

        $this->assertEquals(4, $newState->board->countOwnedCells($BLUE), 'Blue cell count increased');
        $this->assertEquals(1, $newState->board->countOwnedCells($RED), 'Red cell count decreased (piece captured)');
        $this->assertEquals($RED, $newState->getNextPlayer(), 'Player turn proceeded');
    }

    public function testEngineTakesAMove()
    {
        $BLUE = Player::BLUE();
        $RED = Player::RED();

        $state = new GameState();
        $this->assertEquals(2, $state->board->countOwnedCells($BLUE), 'Test precondition');
        $this->assertEquals(2, $state->board->countOwnedCells($RED), 'Test precondition');
        $this->assertEquals($BLUE, $state->getNextPlayer(), 'Test precondition');

        $engine = new Engine($BLUE);
        /* @var $newState GameState */
        $newState = $engine->decide($state);

        $this->assertEquals(4, $newState->board->countOwnedCells($BLUE), 'Blue cell count increased');
        $this->assertEquals(1, $newState->board->countOwnedCells($RED), 'Red cell count decreased (piece captured)');
        $this->assertEquals($RED, $newState->getNextPlayer(), 'Player turn proceeded');
    }

    /**
     * Verify alpha-beta pruning optimization
     *
     * Empyrical test times:
     * Max depth
     *       | Duration normal
     *       |       | Duration AB pruning
     *       |       |      | Nodes evaluated normal
     *       |       |      |       |Nodes evaluated AB pruning
     * ------+-------+------|-------+--------
     *     1 |   0.0 |  0.0 |      5 |     5
     *     2 |   0.0 |  0.0 |     17 |    15
     *     3 |   0.1 |  0.2 |     85 |    67
     *     4 |   0.6 |  0.5 |    365 |   169
     *     5 |   3   |  1   |   2773 |   743
     *     6 |  20   |  5   |  18595 |  2342
     *     7 | 165   | 17   | 199309 | 13958
     */
    public function testEngineHandlesLargeSearchSpace()
    {
        $BLUE = Player::BLUE();
        $RED = Player::RED();
        $maxDepth = 3;

        $state = new GameState();
        $this->assertEquals(2, $state->board->countOwnedCells($BLUE), 'Test precondition');
        $this->assertEquals(2, $state->board->countOwnedCells($RED), 'Test precondition');
        $this->assertEquals($BLUE, $state->getNextPlayer(), 'Test precondition');

        $engine = new Engine($BLUE, $maxDepth);
        /* @var $newState GameState */
        $newState = $engine->decide($state);

        $this->assertEquals(4, $newState->board->countOwnedCells($BLUE), 'Blue cell count increased');
        $this->assertEquals(1, $newState->board->countOwnedCells($RED), 'Red cell count decreased (piece captured)');
        $this->assertEquals($RED, $newState->getNextPlayer(), 'Player turn proceeded');

        $this->assertLessThan(70, $engine->getAnalytics()->nodesEvaluated, 'Evaluated nodes must indicate pruning');
    }
}
