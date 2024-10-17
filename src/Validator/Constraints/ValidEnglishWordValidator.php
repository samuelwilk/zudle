<?php

namespace App\Validator\Constraints;

use App\Entity\Game;
use App\Service\WordListLoaderService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidEnglishWordValidator extends ConstraintValidator
{
    public function __construct(private readonly WordListLoaderService $wordListLoaderService)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidEnglishWord) {
            throw new UnexpectedTypeException($constraint, ValidEnglishWord::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // if this is coming from easy admin, the context object is the game
        if ($this->context->getObject() instanceof Game) {
            $game = $this->context->getObject();
        } else {
            // if this is coming from the form, the context object is the guess
            $game = $this->context->getObject()?->getGame();
        }

        if (!$game instanceof Game) {
            return; // nothing to validate against
        }

        // get the word length
        $wordLength = $game->getWordLength();

        // only validate if the word length is the same as the game's word length
        if (strlen($value) !== $wordLength) {
            return;
        }

        // Get the word list (this will use the cache to load the list)
        $wordList = $this->wordListLoaderService->loadWordList();

        // Check if the word exists in the list
        if (!in_array(strtolower($value), $wordList, true)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('guess')
                ->setParameter('{{ word }}', strtolower($value))
                ->addViolation();
        }
    }
}
