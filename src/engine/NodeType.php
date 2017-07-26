<?php

namespace lucidtaz\minimax\engine;

/**
 * Enum class
 */
final class NodeType
{
    private $value;

    private function __construct()
    {
        // Forbid manual construction
    }

    public static function MIN(): NodeType
    {
        $result = new static();
        $result->value = 'min';
        return $result;
    }

    public static function MAX(): NodeType
    {
        $result = new static();
        $result->value = 'max';
        return $result;
    }
}
