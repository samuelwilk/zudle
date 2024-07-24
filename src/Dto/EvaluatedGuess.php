<?php

namespace App\Dto;

class EvaluatedGuess
{
    /**
     * @param string $guess
     * @param LetterEvaluation[] $letterEvaluations
     */
    public function __construct(private readonly string $guess, private readonly array $letterEvaluations)
    {
    }

    public function getGuess(): string
    {
        return $this->guess;
    }

    public function getLetterEvaluations(): array
    {
        return $this->letterEvaluations;
    }
}
