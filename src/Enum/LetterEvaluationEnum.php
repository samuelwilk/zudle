<?php

namespace App\Enum;

use App\Enum\Trait\BeautifyEnumKeyTrait;

enum LetterEvaluationEnum: int
{
    use BeautifyEnumKeyTrait;

    case CORRECT = 1;
    case PRESENT = 2;
    case ABSENT = 3;
}
