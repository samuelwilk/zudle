<?php

namespace App\Enum;

use App\Enum\Trait\BeautifyEnumKeyTrait;

enum GameStatusEnum: int
{
    use BeautifyEnumKeyTrait;

    case WON = 1;
    case LOST = 2;
    case IN_PROGRESS = 3;
}
