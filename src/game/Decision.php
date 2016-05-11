<?php

namespace lucidtaz\minimax\game;

interface Decision
{
    /**
     * Mutates a GameState to a new GameState
     */
    public function apply(GameState $sourceState): GameState;
}
