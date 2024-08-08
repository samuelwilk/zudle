<?php

namespace App\Service;

use App\Dto\GuessEvaluationPercentagesDto;
use App\Entity\Team;
use App\Entity\User;
use App\Enum\GameStatusEnum;
use App\Enum\LetterEvaluationEnum;
use App\Enum\StatsChartTimeFrameEnum;
use App\Enum\StatsChartTypeEnum;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class StatsService
{
    public function __construct(
        private readonly GameService $gameService,
        private readonly GuessService $guessService,
        private readonly TeamRepository $teamRepository,
        private readonly UserRepository $userRepository,
        private readonly ChartBuilderInterface $chartBuilder,
    ) {
    }

    public function getTeamWinStats(?Team $team = null): array
    {
        return $this->teamRepository->getTeamWinStats($team);
    }

    public function getPlayerWinStats(?User $user = null): array
    {
        return $this->userRepository->getPlayerWinStats($user);
    }

    public function calculateGuessEvaluationPercentages(?User $user = null): GuessEvaluationPercentagesDto
    {
        $percentages = new GuessEvaluationPercentagesDto(0, 0, 0);

        if (null == $user) {
            return $percentages;
        }

        $guesses = $this->guessService->findGuessesByUser($user);
        $totalLetterGuesses = 0;
        $evaluationCounts = [
            LetterEvaluationEnum::PRESENT->value => $percentages->getPresent(),
            LetterEvaluationEnum::ABSENT->value => $percentages->getAbsent(),
            LetterEvaluationEnum::CORRECT->value => $percentages->getCorrect(),
        ];

        foreach ($guesses as $guess) {
            $evaluatedGuesses = $this->gameService->evaluateGuesses([$guess], $guess->getGame());
            foreach ($evaluatedGuesses as $evaluatedGuess) {
                foreach ($evaluatedGuess->getLetterEvaluations() as $evaluation) {
                    ++$totalLetterGuesses;
                    ++$evaluationCounts[$evaluation->getEvaluation()->value];
                }
            }
        }

        if ($totalLetterGuesses > 0) {
            foreach ($evaluationCounts as $type => $count) {
                switch ($type) {
                    case LetterEvaluationEnum::PRESENT->value:
                        $percentages->setPresent(($count / $totalLetterGuesses) * 100);
                        break;
                    case LetterEvaluationEnum::ABSENT->value:
                        $percentages->setAbsent(($count / $totalLetterGuesses) * 100);
                        break;
                    case LetterEvaluationEnum::CORRECT->value:
                        $percentages->setCorrect(($count / $totalLetterGuesses) * 100);
                        break;
                }
            }
        }

        return $percentages;
    }

    public function getGamesGroupedByDay(\DateTime $start, \DateTime $end): array
    {
        // Fetch games created within the current month
        $gamesThisMonth = $this->gameService->getGamesBetweenDates($start, $end);

        $groupedGames = [];
        foreach ($gamesThisMonth as $game) {
            $dayOfMonth = $game->getCreatedAt()->format('j'); // Day of the month as key
            if (!isset($groupedGames[$dayOfMonth])) {
                $groupedGames[$dayOfMonth] = [];
            }
            $groupedGames[$dayOfMonth][] = $game;
        }

        return $groupedGames;
    }

    public function getGuessesGroupedByDay(\DateTime $start, \DateTime $end): array
    {
        // Fetch games created within the current month
        $gamesThisMonth = $this->guessService->getGuessesBetweenDates($start, $end);

        $groupedGames = [];
        foreach ($gamesThisMonth as $game) {
            $date = $game->getCreatedAt()->format('Y-m-d'); // Day of the month as key
            if (!isset($groupedGames[$date])) {
                $groupedGames[$date] = [];
            }
            $groupedGames[$date][] = $game;
        }

        return $groupedGames;
    }

    public function getNumberOfGames(?\DateTime $start = null, ?\DateTime $end = null): int
    {
        return $this->gameService->countGamesBetweenDates($start, $end);
    }

    public function getNumberGuesses(?\DateTime $start = null, ?\DateTime $end = null): int
    {
        return $this->guessService->countGuessesBetweenDates($start, $end);
    }

    public function getGamesTrend(): float
    {
        $today = new \DateTime();
        $startCurrentPeriod = (clone $today)->modify('-30 days')->setTime(0, 0);
        $endCurrentPeriod = (clone $today)->setTime(23, 59, 59);

        $startPreviousPeriod = (clone $startCurrentPeriod)->modify('-30 days')->setTime(0, 0);
        $endPreviousPeriod = (clone $startCurrentPeriod)->modify('-1 second');

        $currentPeriodCount = $this->gameService->countGamesBetweenDates($startCurrentPeriod, $endCurrentPeriod);
        $previousPeriodCount = $this->gameService->countGamesBetweenDates($startPreviousPeriod, $endPreviousPeriod);

        if (0 === $previousPeriodCount) {
            return $currentPeriodCount > 0 ? 100.0 : 0.0;
        }

        return (($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100;
    }

    public function getGuessTrend(): float
    {
        $today = new \DateTime();
        $startCurrentPeriod = (clone $today)->modify('-30 days')->setTime(0, 0);
        $endCurrentPeriod = (clone $today)->setTime(23, 59, 59);

        $startPreviousPeriod = (clone $startCurrentPeriod)->modify('-30 days')->setTime(0, 0);
        $endPreviousPeriod = (clone $startCurrentPeriod)->modify('-1 second');

        $currentPeriodCount = $this->guessService->countGuessesBetweenDates($startCurrentPeriod, $endCurrentPeriod);
        $previousPeriodCount = $this->guessService->countGuessesBetweenDates($startPreviousPeriod, $endPreviousPeriod);

        if (0 === $previousPeriodCount) {
            return $currentPeriodCount > 0 ? 100.0 : 0.0;
        }

        return (($currentPeriodCount - $previousPeriodCount) / $previousPeriodCount) * 100;
    }

    public function getGamesCompletedWithGuesses(): array
    {
        $games = $this->gameService->getGamesByStatus(GameStatusEnum::WON);
        $totalGames = count($games);
        $guessCounts = array_fill(1, 5, 0);

        foreach ($games as $game) {
            $attempts = $game->getAttempts();
            if ($attempts >= 1 && $attempts <= 5) {
                ++$guessCounts[$attempts];
            }
        }

        if ($totalGames > 0) {
            foreach ($guessCounts as $attempts => $count) {
                $guessCounts[$attempts] = ($count / $totalGames) * 100;
            }
        }

        return $guessCounts;
    }

    public function fetchChart(StatsChartTypeEnum $statsChartTypeEnum, StatsChartTimeFrameEnum $statsChartTimeFrameEnum): Chart
    {
        return match ($statsChartTypeEnum) {
            StatsChartTypeEnum::GAMES_OVER_TIME => $this->getGamesOverTimeChart($statsChartTimeFrameEnum),
            StatsChartTypeEnum::GUESSES_OVER_TIME => $this->getGuessesOverTimeChart($statsChartTimeFrameEnum),
            StatsChartTypeEnum::GAME_STATUS_DISTRIBUTION => $this->getGameStatusDistributionChart($statsChartTimeFrameEnum),
            StatsChartTypeEnum::TOP_PLAYERS => new Chart(Chart::TYPE_PIE),
            StatsChartTypeEnum::AVERAGE_ATTEMPTS_PER_GAME => new Chart(Chart::TYPE_PIE),
            StatsChartTypeEnum::GUESS_ACCURACY => new Chart(Chart::TYPE_PIE),
            StatsChartTypeEnum::WORD_DIFFICULTY => new Chart(Chart::TYPE_PIE),
        };
    }

    private function getGamesOverTimeChart(StatsChartTimeFrameEnum $statsChartTimeFrameEnum): Chart
    {
        $now = new \DateTime(); // Current date
        $startDate = match ($statsChartTimeFrameEnum) {
            StatsChartTimeFrameEnum::TODAY => (clone $now)->setTime(0, 0),
            StatsChartTimeFrameEnum::YESTERDAY => (clone $now)->modify('-1 day')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_7_DAYS => (clone $now)->modify('-7 days')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_30_DAYS => (clone $now)->modify('-30 days')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_90_DAYS => (clone $now)->modify('-90 days')->setTime(0, 0),
        };

        $gamesOverTime = $this->getGamesGroupedByDay($startDate, $now);

        $labels = [];
        $data = [];
        foreach ($gamesOverTime as $day => $games) {
            $labels[] = $day;
            $data[] = count($games);
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Games Over Time',
                    'data' => $data,
                ],
            ],
        ]);
        $chart->setOptions([
            'plugins' => [
                'colors' => true,
            ],
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => !empty($data) ? max($data) + 10 : 10,
                ],
            ],
        ]);

        return $chart;
    }

    private function getGuessesOverTimeChart(StatsChartTimeFrameEnum $statsChartTimeFrameEnum): Chart
    {
        $now = new \DateTime(); // Current date
        $startDate = match ($statsChartTimeFrameEnum) {
            StatsChartTimeFrameEnum::TODAY => (clone $now)->setTime(0, 0),
            StatsChartTimeFrameEnum::YESTERDAY => (clone $now)->modify('-1 day')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_7_DAYS => (clone $now)->modify('-7 days')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_30_DAYS => (clone $now)->modify('-30 days')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_90_DAYS => (clone $now)->modify('-90 days')->setTime(0, 0),
        };

        $gamesOverTime = $this->getGuessesGroupedByDay($startDate, $now);

        $labels = [];
        $data = [];
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($startDate, $interval, $now);

        foreach ($period as $date) {
            $labels[] = $date->format('F jS'); // Format as "Month Day"
            if (isset($gamesOverTime[$date->format('Y-m-d')])) {
                $data[] = count($gamesOverTime[$date->format('Y-m-d')]);
            } else {
                $data[] = 0;
            }
        }

        $data = array_values($data); // Ensure data array is indexed correctly

        // Step 3: Update the chart creation code
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Guesses',
                    'data' => $data,
                ],
            ],
        ]);
        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => !empty($data) ? max($data) + 10 : 10,
                ],
            ],
        ]);

        return $chart;
    }

    private function getGameStatusDistributionChart(StatsChartTimeFrameEnum $statsChartTimeFrameEnum): Chart
    {
        $now = new \DateTime(); // Current date
        $startDate = match ($statsChartTimeFrameEnum) {
            StatsChartTimeFrameEnum::TODAY => (clone $now)->setTime(0, 0),
            StatsChartTimeFrameEnum::YESTERDAY => (clone $now)->modify('-1 day')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_7_DAYS => (clone $now)->modify('-7 days')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_30_DAYS => (clone $now)->modify('-30 days')->setTime(0, 0),
            StatsChartTimeFrameEnum::LAST_90_DAYS => (clone $now)->modify('-90 days')->setTime(0, 0),
        };

        $gameStatusCounts = $this->gameService->getGameStatusCounts($startDate, $now);

        $labels = [];
        $data = [];
        foreach ($gameStatusCounts as $status => $count) {
            $labels[] = GameStatusEnum::from($status)->beautifyEnumKey();
            $data[] = $count;
        }

        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Game Status Distribution',
                    'data' => $data,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => !empty($data) ? max($data) + 10 : 10,
                ],
            ],
        ]);

        return $chart;
    }

    public function getChartTitle(StatsChartTypeEnum $statsChartTypeEnum, StatsChartTimeFrameEnum $statsChartTimeFrameEnum): string
    {
        return match ($statsChartTypeEnum) {
            StatsChartTypeEnum::GAMES_OVER_TIME => 'User Participation for '.StatsChartTimeFrameEnum::formatForChatTitle($statsChartTimeFrameEnum),
            StatsChartTypeEnum::GAME_STATUS_DISTRIBUTION => 'Game Status Distribution for '.StatsChartTimeFrameEnum::formatForChatTitle($statsChartTimeFrameEnum),
            StatsChartTypeEnum::TOP_PLAYERS => 'Top Players for '.StatsChartTimeFrameEnum::formatForChatTitle($statsChartTimeFrameEnum),
            StatsChartTypeEnum::AVERAGE_ATTEMPTS_PER_GAME => 'Average Attempts per Game for '.StatsChartTimeFrameEnum::formatForChatTitle($statsChartTimeFrameEnum),
            StatsChartTypeEnum::GUESS_ACCURACY => 'Guess Accuracy for '.StatsChartTimeFrameEnum::formatForChatTitle($statsChartTimeFrameEnum),
            StatsChartTypeEnum::WORD_DIFFICULTY => 'Word Difficulty for '.StatsChartTimeFrameEnum::formatForChatTitle($statsChartTimeFrameEnum),
        };
    }
}
