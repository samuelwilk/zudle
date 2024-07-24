<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\EnumExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * This extension allows to get enum label from enum value and a enum class name.
 */
class EnumExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('get_enum_label', [EnumExtensionRuntime::class, 'getEnumLabel']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('beautify_enum', [EnumExtensionRuntime::class, 'beautifyEnum']),
            new TwigFunction('get_enum_label', [EnumExtensionRuntime::class, 'getEnumLabel']),
            new TwigFunction('get_int_enum_value', [EnumExtensionRuntime::class, 'getIntEnumValue']),
            new TwigFunction('beautify_enum_value', [EnumExtensionRuntime::class, 'beatifyEnumValue']),
        ];
    }
}
