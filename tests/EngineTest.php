<?php

namespace lucidtaz\minimax\tests;

use lucidtaz\minimax\Engine;
use lucidtaz\minimax\tests\tictactoe\GameState;
use lucidtaz\minimax\tests\tictactoe\Player;
use PHPUnit_Framework_TestCase;

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

    public function testEngineTakesAWin()
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

    public function testEnginePreventsALoss()
    {
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

    public function testEngineForcesAWin()
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
}
