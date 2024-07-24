<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class IsValidClassName extends Constraint
{
    public string $message = 'The string "{{ string }}" is not a valid class name.';
}
