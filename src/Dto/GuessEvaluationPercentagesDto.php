<?php

namespace App\Dto;

class GuessEvaluationPercentagesDto
{
    public function __construct(private float $present, private float $absent, private float $correct)
    {
    }

    public function getPresent(): float
    {
        return $this->present;
    }

    public function setPresent(float $present): void
    {
        $this->present = $present;
    }

    public function getAbsent(): float
    {
        return $this->absent;
    }

    public function setAbsent(float $absent): void
    {
        $this->absent = $absent;
    }

    public function getCorrect(): float
    {
        return $this->correct;
    }

    public function setCorrect(float $correct): void
    {
        $this->correct = $correct;
    }
}
