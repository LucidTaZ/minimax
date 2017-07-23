[![Build Status](https://travis-ci.org/LucidTaZ/minimax.svg?branch=master)](https://travis-ci.org/LucidTaZ/minimax)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/LucidTaZ/minimax/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/LucidTaZ/minimax/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/LucidTaZ/minimax/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/LucidTaZ/minimax/?branch=master)

MiniMax engine in PHP
=====================

This library provides easy integration of the MiniMax game decision making
algorithm into your game, using a simple interface to separate the algorithm
from the game logic.

Usage
-----

To use this library, first make sure you implement each interface in
`lucidtaz\minimax\game`.

Then, simply construct an instance of `lucidtaz\minimax\engine\Engine`, give it
the `Player` to act as, and when it is the player's turn, call the `decide()`
method. This will result in the `GameState` instance that results after the
engine takes its move.

In code:

```php
class MyPlayer implements \lucidtaz\minimax\game\Player
{
    ...
}

class MyGameState implements \lucidtaz\minimax\game\GameState
{
    ...
}

$player = new MyPlayer(...);
$engine = new \lucidtaz\minimax\engine\Engine($player);

$gameState = new MyGameState(...);

$newGameState = $engine->decide($gameState);
```

For an example, see the `tests/tictactoe` directory.
