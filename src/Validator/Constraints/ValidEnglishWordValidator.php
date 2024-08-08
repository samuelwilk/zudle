<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ValidEnglishWordValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ValidEnglishWord) {
            throw new UnexpectedTypeException($constraint, ValidEnglishWord::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        // TODO: Implement the custom validation here

        //// Initialize the Enchant broker and dictionary
        //$broker = enchant_broker_init();
        //$dict = enchant_broker_request_dict($broker, 'en_US');
        //
        //if (!$dict) {
        //    $this->context->buildViolation('Could not initialize the dictionary.')
        //        ->addViolation();
        //
        //    return;
        //}
        //
        //// Check if the word is valid
        //if (!enchant_dict_check($dict, $value)) {
        //    $this->context->buildViolation($constraint->message)
        //        ->setParameter('{{ string }}', $value)
        //        ->addViolation();
        //}
        //
        //// Unset the dictionary and broker objects
        //unset($dict);
        //unset($broker);
    }
}
