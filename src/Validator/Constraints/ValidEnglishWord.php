<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidEnglishWord extends Constraint
{
    public string $message = 'The word "{{ string }}" is not a valid English word.';
}
