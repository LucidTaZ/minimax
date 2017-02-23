<?php

namespace lucidtaz\minimax\game;

/**
 * Representation of a move in the game
 *
 * An object of this class carries all information needed to transition the game
 * from one GameState to the next. The AI engine uses this in an immutable
 * fashion, to evaluate possible future moves.
 *
 * The emptiness of this class may seem odd. It exists to satisfy type hinting
 * and static code analysis, and to guide the implementer to what is needed.
 */
interface Decision
{

}
