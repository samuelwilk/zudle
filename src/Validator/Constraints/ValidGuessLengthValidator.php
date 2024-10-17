<?php

namespace App\Validator\Constraints;

use App\Entity\Game;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidGuessLengthValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidGuessLength) {
            throw new UnexpectedTypeException($constraint, ValidGuessLength::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        /** @var Game $game */
        $game = $this->context->getObject();
        $gameWordLength = $game->getWordLength();

        foreach ($game->getGuesses() as $guess) {
            if (strlen($guess->getGuess()) !== $gameWordLength) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('guesses')
                    ->addViolation();
                break;
            }
        }
    }
}
