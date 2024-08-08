<?php

namespace App\Enum;

use App\Enum\Trait\BeautifyEnumKeyTrait;
use Symfony\UX\Chartjs\Model\Chart;

enum StatsChartTypeEnum: int
{
    use BeautifyEnumKeyTrait;

    case GAMES_OVER_TIME = 1;
    case GUESSES_OVER_TIME = 2;
    case GAME_STATUS_DISTRIBUTION = 3;
    case TOP_PLAYERS = 4;
    case AVERAGE_ATTEMPTS_PER_GAME = 5;
    case GUESS_ACCURACY = 6;
    case WORD_DIFFICULTY = 7;

    public static function getChartType(StatsChartTypeEnum $statsChartTypeEnum): string
    {
        return match ($statsChartTypeEnum) {
            StatsChartTypeEnum::GAMES_OVER_TIME, StatsChartTypeEnum::GUESSES_OVER_TIME, StatsChartTypeEnum::AVERAGE_ATTEMPTS_PER_GAME => Chart::TYPE_LINE,
            StatsChartTypeEnum::GAME_STATUS_DISTRIBUTION => Chart::TYPE_PIE,
            StatsChartTypeEnum::TOP_PLAYERS, StatsChartTypeEnum::WORD_DIFFICULTY => Chart::TYPE_BAR,
            StatsChartTypeEnum::GUESS_ACCURACY => Chart::TYPE_SCATTER,
        };
    }
}
