<?php

namespace lucidtaz\minimax\game;

/**
 * Player participating in the game
 *
 * This simple interface is needed to represent the notion of "self" and
 * "hostility".
 */
interface Player
{
    /**
     * Returns whether the given Player is this instance
     *
     * When $other is equal to $this, the result should always be true!
     * Alternatively, if there is enough information in the state of the object
     * to uniquely identify the player (such as an ID), then that may be
     * compared as well.
     */
    public function equals(Player $other): bool;

    /**
     * Returns whether the given Player is friends with this instance
     * Useful in multi-player games
     */
    public function isFriendsWith(Player $other): bool;
}
