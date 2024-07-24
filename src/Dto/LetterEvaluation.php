<?php

namespace App\Dto;

use App\Enum\LetterEvaluationEnum;

readonly class LetterEvaluation
{
    public function __construct(private string $letter, private LetterEvaluationEnum $evaluation)
    {
    }

    public function getLetter(): string
    {
        return $this->letter;
    }

    public function getEvaluation(): LetterEvaluationEnum
    {
        return $this->evaluation;
    }
}
