<?php

namespace lucidtaz\minimax\tests\tictactoe;

use lucidtaz\minimax\game\Player as PlayerInterface;

class Player implements PlayerInterface
{
    private $sign;

    public static function NONE(): Player
    {
        $result = new Player;
        $result->sign = ' ';
        return $result;
    }

    public static function X(): Player
    {
        $result = new Player;
        $result->sign = 'x';
        return $result;
    }

    public static function O(): Player
    {
        $result = new Player;
        $result->sign = 'o';
        return $result;
    }

    public function equals(PlayerInterface $other): bool
    {
        return $other->sign == $this->sign;
    }

    public function isFriendsWith(PlayerInterface $other): bool
    {
        return $this->equals($other);
    }
}
