<?php

namespace App\Enum\Trait;

trait BeautifyEnumKeyTrait
{
    /**
     * Helper method to make enum key look pretty.
     * (remove underscores and only caps on first letter of each word).
     */
    public function beautifyEnumKey(): string
    {
        return ucwords(mb_strtolower(str_replace('_', ' ', $this->name)));
    }
}
