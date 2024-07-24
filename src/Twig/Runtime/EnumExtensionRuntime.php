<?php

namespace App\Twig\Runtime;

use http\Exception\InvalidArgumentException;
use Twig\Extension\RuntimeExtensionInterface;

class EnumExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function getEnumLabel(string $enum, int $value): string
    {
        // get enum from app/Enum/$enum.php
        $enum = "App\\Enum\\$enum";

        if (!class_exists($enum)) {
            return 'Unable to determine values enumerator.';
        }

        return $enum::from($value)->name;
    }

    public function getIntEnumValue(string $enum, string $key): int
    {
        // get enum from app/Enum/$enum.php
        $enum = "App\\Enum\\$enum";

        if (!class_exists($enum)) {
            throw new InvalidArgumentException('Unable to determine values enumerator.');
        }

        $cases = $enum::cases();

        if (!is_int($cases[0]->value)) {
            throw new InvalidArgumentException('Cannot determine the integer value of the enum. Ensure this enum is of type int.');
        }

        foreach ($cases as $case) {
            if ($case->name === $key) {
                return $case->value;
            }
        }

        throw new InvalidArgumentException('Unable to determine the integer value of the enum.');
    }

    public function beatifyEnumValue(string $enum, int $value): string
    {
        // get enum from app/Enum/$enum.php
        $enum = "App\\Enum\\$enum";

        if (!class_exists($enum)) {
            return 'Unable to determine values enumerator.';
        }

        // check if this enum has BeautifyEnumKeyTrait
        if (!in_array('App\\Enum\\Trait\\BeautifyEnumKeyTrait', class_uses($enum), true)) {
            return 'Unable to beautify the enum key. Ensure this enum has BeautifyEnumKeyTrait.';
        }

        return $enum::from($value)->beautifyEnumKey();
    }
}
