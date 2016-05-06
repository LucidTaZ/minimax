<?php

namespace lucidtaz\minimax;

interface Decision {
    /**
     * Mutates a GameState to a new GameState
     */
    public function apply(GameState $sourceState): GameState;
}
