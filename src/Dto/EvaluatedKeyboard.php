<?php

namespace App\Dto;

use App\Enum\LetterEvaluationEnum;

class EvaluatedKeyboard
{
    private array $evaluatedKeyboard;

    public function __construct(array $evaluatedGuesses)
    {
        // initialize evaluatedKeyboard with each letter in the alphabet
        $this->evaluatedKeyboard = array_fill_keys(range('A', 'Z'), null);

        // loop over the guesses and update the evaluatedKeyboard
        // update the value in evaluatedKeyboard based on the following rules:
        // check if the guess's letter is present in the word, then update the value to its corresponding LetterEvaluationEnum
        // update the value if the guess's letter is already present in the evaluatedKeyboard
        // do not update the value if the guess's letter is already correct in the evaluatedKeyboard
        foreach ($evaluatedGuesses as $evaluatedGuess) {
            foreach ($evaluatedGuess->getLetterEvaluations() as $letterEvaluation) {
                $letter = strtoupper($letterEvaluation->getLetter());
                $evaluation = $letterEvaluation->getEvaluation();

                switch ($evaluation) {
                    case LetterEvaluationEnum::PRESENT:
                    case LetterEvaluationEnum::ABSENT:
                        // only update if the existing letter is not correct
                        if (LetterEvaluationEnum::CORRECT !== $this->evaluatedKeyboard[$letter]) {
                            $this->evaluatedKeyboard[$letter] = $evaluation;
                        }
                        break;
                    case LetterEvaluationEnum::CORRECT:
                        $this->evaluatedKeyboard[$letter] = $evaluation;
                        break;
                    default:
                        break;
                }
            }
        }
    }

    public function getEvaluatedKeyboard(): array
    {
        return $this->evaluatedKeyboard;
    }
}
