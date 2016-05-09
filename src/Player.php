<?php

namespace lucidtaz\minimax;

interface Player
{
    /**
     * Returns whether the given Player is this instance
     */
    public function equals(Player $other): bool;

    /**
     * Returns whether the given Player is friends with this instance
     * Useful in multi-player games
     */
    public function isFriendsWith(Player $other): bool;
}
