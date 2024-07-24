<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class IsValidClassNameValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidClassName) {
            throw new UnexpectedTypeException($constraint, IsValidClassName::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            // Throw this exception if your validator cannot handle the passed type so that it can be marked as invalid
            throw new UnexpectedValueException($value, 'string');
        }

        // Check if the class name is valid
        if (!class_exists($value)) {
            // The argument must be a string or an object.
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
