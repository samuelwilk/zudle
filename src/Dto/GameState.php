<?php

namespace App\Dto;

use App\Entity\Game;

readonly class GameState
{
    /**
     * @param EvaluatedGuess[] $evaluatedGuesses
     */
    public function __construct(
        private Game $game,
        private int $attemptsLeft,
        private array $evaluatedGuesses,
        private array $evaluatedKeyboard,
    ) {
    }

    public function getGame(): Game
    {
        return $this->game;
    }

    public function getAttemptsLeft(): int
    {
        return $this->attemptsLeft;
    }

    public function getEvaluatedGuesses(): array
    {
        return $this->evaluatedGuesses;
    }

    public function getEvaluatedKeyboard(): array
    {
        return $this->evaluatedKeyboard;
    }
}
