<?php

namespace App\Twig\Components;

use App\Enum\StatsChartTimeFrameEnum;
use App\Enum\StatsChartTypeEnum;
use App\Form\StatsChartFormType;
use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsLiveComponent]
class StatsChart extends AbstractController
{
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public int $fromTime = StatsChartTimeFrameEnum::LAST_7_DAYS->value;

    #[LiveProp(writable: true)]
    public int $currentChartType = StatsChartTypeEnum::GAMES_OVER_TIME->value;

    public function __construct(
        private StatsService $statsService,
        private ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function getChart(): Chart
    {
        $statsChartTypeEnum = StatsChartTypeEnum::from($this->currentChartType);
        $statsChartTimeFrameEnum = StatsChartTimeFrameEnum::from($this->fromTime);

        return $this->statsService->fetchChart(
            $statsChartTypeEnum,
            $statsChartTimeFrameEnum,
        );
    }

    #[ExposeInTemplate]
    public function getChartTypes(): array
    {
        return [
            'Games Over Time' => StatsChartTypeEnum::GAMES_OVER_TIME->value,
            'Guesses Over Time' => StatsChartTypeEnum::GUESSES_OVER_TIME->value,
            'Game Status Distribution' => StatsChartTypeEnum::GAME_STATUS_DISTRIBUTION->value,
            'Top Players' => StatsChartTypeEnum::TOP_PLAYERS->value,
            'Average Attempts Per Game' => StatsChartTypeEnum::AVERAGE_ATTEMPTS_PER_GAME->value,
            'Guess Accuracy' => StatsChartTypeEnum::GUESS_ACCURACY->value,
            'Word Difficulty' => StatsChartTypeEnum::WORD_DIFFICULTY->value,
        ];
    }

    #[ExposeInTemplate]
    public function getTimeFrames(): array
    {
        return [
            StatsChartTimeFrameEnum::TODAY->beautifyEnumKey() => StatsChartTimeFrameEnum::TODAY->value,
            StatsChartTimeFrameEnum::YESTERDAY->beautifyEnumKey() => StatsChartTimeFrameEnum::YESTERDAY->value,
            StatsChartTimeFrameEnum::LAST_7_DAYS->beautifyEnumKey() => StatsChartTimeFrameEnum::LAST_7_DAYS->value,
            StatsChartTimeFrameEnum::LAST_30_DAYS->beautifyEnumKey() => StatsChartTimeFrameEnum::LAST_30_DAYS->value,
            StatsChartTimeFrameEnum::LAST_90_DAYS->beautifyEnumKey() => StatsChartTimeFrameEnum::LAST_90_DAYS->value,
        ];
    }
}
