<?php

// src/Validator/Constraints/ValidGuessLength.php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ValidGuessLength extends Constraint
{
    public string $message = 'The length of each guess must match the length of the game word.';
}
