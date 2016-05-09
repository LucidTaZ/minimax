<?php

namespace lucidtaz\minimax\tests;

use LogicException;
use lucidtaz\minimax\Engine;
use lucidtaz\minimax\tests\tictactoe\GameState;
use lucidtaz\minimax\tests\tictactoe\Player;
use PHPUnit_Framework_TestCase;
use RuntimeException;

class EngineTest extends PHPUnit_Framework_TestCase
{
    public function testEngineDecides()
    {
        $cleanState = new GameState;
        $engine = new Engine(Player::X());

        $this->assertEquals(0, $this->countFilledFields($cleanState), 'Test precondition');
        $decision = $engine->decide($cleanState);
        $newState = $decision->apply($cleanState);
        $this->assertEquals(1, $this->countFilledFields($newState));
    }

    public function testEngineDecidesWhenAllScoresZero()
    {
        // Regression test: it used to say "no possible moves".
        $cleanState = $this->getMockBuilder(GameState::class)
            ->setMethods(['evaluateScore'])
            ->getMock();
        $cleanState->expects($this->atLeastOnce())
            ->method('evaluateScore')
            ->willReturn(0);
        $engine = new Engine(Player::X());

        $this->assertEquals(0, $this->countFilledFields($cleanState), 'Test precondition');
        $decision = $engine->decide($cleanState);
        $newState = $decision->apply($cleanState);
        $this->assertEquals(1, $this->countFilledFields($newState));
    }

    private function countFilledFields(GameState $state): int
    {
        $count = 0;
        for ($row = 0; $row < 3; $row++) {
            for ($col = 0; $col < 3; $col++) {
                if ($state->getField($row, $col) != Player::NONE()) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function testEngineTakesAWin1()
    {
        $X = Player::X();

        $state = new GameState;
        // XX
        //  O
        //   O
        $state->fillField(0, 0); // X
        $state->fillField(1, 1); // O
        $state->fillField(0, 1); // X
        $state->fillField(2, 2); // O

        $engine = new Engine($X);
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(0, 2)->equals($X), 'Upper-right field must be taken by X');
    }

    public function testEngineTakesAWin2()
    {
        // TODO: Fix this failing test
        // After fixing, introduce a shuffle in tictactoe\GameState::getDecisions() to verify that shuffling does not produce fails

        // Rotated version of another test, to rule out accidental, unstrategic wins
        $X = Player::X();

        $state = new GameState;
        // O
        //  O
        //  XX
        $state->fillField(2, 2); // X
        $state->fillField(1, 1); // O
        $state->fillField(2, 1); // X
        $state->fillField(0, 0); // O

        $engine = new Engine($X); // Note: this test only fails with maxDepth>=3
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(2, 0)->equals($X), 'Lower-left field must be taken by X');
    }

    public function testEnginePreventsALoss1()
    {
        $X = Player::X();

        $state = new GameState;
        // X
        // X
        // OO
        $state->fillField(0, 0); // X
        $state->fillField(2, 0); // O
        $state->fillField(1, 0); // X
        $state->fillField(2, 1); // O

        $engine = new Engine($X);
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(2, 2)->equals($X), 'Lower-right field must be taken by X');
    }

    public function testEnginePreventsALoss2()
    {
        // Rotated version of another test, to rule out accidental, unstrategic wins
        $X = Player::X();

        $state = new GameState;
        // OXX
        // O
        //
        $state->fillField(0, 1); // X
        $state->fillField(0, 0); // O
        $state->fillField(0, 2); // X
        $state->fillField(1, 0); // O

        $engine = new Engine($X);
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(2, 0)->equals($X), 'Lower-left field must be taken by X');
    }

    public function testEngineForcesAWin1()
    {
        $X = Player::X();

        $state = new GameState;
        // O X
        //  O
        // X
        $state->fillField(2, 0); // X
        $state->fillField(0, 0); // O
        $state->fillField(0, 2); // X
        $state->fillField(1, 1); // O

        $engine = new Engine($X);
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(2, 2)->equals($X), 'Bottom-right field must be taken by X');
    }

    public function testEngineForcesAWin2()
    {
        // Rotated version of another test, to rule out accidental, unstrategic wins
        $X = Player::X();

        $state = new GameState;
        // X O
        //  O
        //   X
        $state->fillField(2, 2); // X
        $state->fillField(0, 2); // O
        $state->fillField(0, 0); // X
        $state->fillField(1, 1); // O

        $engine = new Engine($X);
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(2, 0)->equals($X), 'Bottom-left field must be taken by X');
    }

    public function testEnginePlaysAgainstItself()
    {
        // Tic Tac Toe Minimax against itself should always result in a draw
        $X = Player::X();
        $O = Player::O();

        $state = new GameState;

        $engineX = new Engine($X, 4);
        $engineO = new Engine($O, 4);

        for ($i = 0; $i < 8; $i += 2) {
            $decisionX = $engineX->decide($state);
            $state = $decisionX->apply($state);
            $decisionO = $engineO->decide($state);
            $state = $decisionO->apply($state);
        }
        $lastDecisionX = $engineX->decide($state);
        $state = $lastDecisionX->apply($state);

        $this->assertEquals(9, $this->countFilledFields($state), 'All fields must be full');
        $this->assertEquals(0, $state->evaluateScore($X), 'Player X must not win');
        $this->assertEquals(0, $state->evaluateScore($O), 'Player O must not win');
    }

    public function testEngineHandlesOneDrawOption()
    {
        $X = Player::X();

        $state = new GameState;
        // OXX
        //  OO
        // XOX
        $state->fillField(2, 0); // X
        $state->fillField(0, 0); // O
        $state->fillField(0, 2); // X
        $state->fillField(1, 1); // O
        $state->fillField(0, 1); // X
        $state->fillField(2, 1); // O
        $state->fillField(2, 2); // X
        $state->fillField(1, 2); // O

        $engine = new Engine($X);
        $decision = $engine->decide($state);
        $newState = $decision->apply($state);

        $this->assertTrue($newState->getField(1, 0)->equals($X), 'Middle-left field must be taken by X');
    }

    public function testExceptionWhenNotOurTurn()
    {
        $cleanState = new GameState;
        $engine = new Engine(Player::O());

        $this->expectException(LogicException::class);
        $engine->decide($cleanState);
    }

    public function testExceptionWhenNoMovesLeft()
    {
        $O = Player::O();

        $state = new GameState;
        // OXX
        // XOO
        // XOX
        $state->fillField(2, 0); // X
        $state->fillField(0, 0); // O
        $state->fillField(0, 2); // X
        $state->fillField(1, 1); // O
        $state->fillField(0, 1); // X
        $state->fillField(2, 1); // O
        $state->fillField(2, 2); // X
        $state->fillField(1, 2); // O
        $state->fillField(1, 0); // X

        $engine = new Engine($O);

        $this->expectException(RuntimeException::class);
        $engine->decide($state);
    }
}
