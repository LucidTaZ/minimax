<?php

namespace lucidtaz\minimax\game;

/**
 * Representation of a move in the game
 *
 * An object of this class carries all information needed to transition the game
 * from one GameState to the next. The AI engine uses this in an immutable
 * fashion, to evaluate possible future moves.
 *
 * If mutability is desired, give a copy of the GameState to the
 * Engine::decide() method, then apply the resulting decision on the original
 * GameState yourself.
 */
interface Decision
{
    /**
     * Mutates a GameState to a new GameState
     *
     * Do not update $sourceState, it is immutable! Note: this may be relaxed by
     * explicitly passing a clone from the engine, making the library a bit
     * friendlier.
     */
    public function apply(GameState $sourceState): GameState;
}
