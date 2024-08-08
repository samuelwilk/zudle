<?php

namespace App\Enum;

use App\Enum\Trait\BeautifyEnumKeyTrait;

enum StatsChartTimeFrameEnum: int
{
    use BeautifyEnumKeyTrait;

    case TODAY = 1;
    case YESTERDAY = 2;
    case LAST_7_DAYS = 3;
    case LAST_30_DAYS = 4;
    case LAST_90_DAYS = 5;

    public static function formatForChatTitle(StatsChartTimeFrameEnum $statsChartTimeFrameEnum): string
    {
        return match ($statsChartTimeFrameEnum) {
            StatsChartTimeFrameEnum::TODAY, StatsChartTimeFrameEnum::YESTERDAY => $statsChartTimeFrameEnum->beautifyEnumKey(),
            StatsChartTimeFrameEnum::LAST_7_DAYS, StatsChartTimeFrameEnum::LAST_30_DAYS, StatsChartTimeFrameEnum::LAST_90_DAYS => 'the '.$statsChartTimeFrameEnum->beautifyEnumKey(),
        };
    }
}
