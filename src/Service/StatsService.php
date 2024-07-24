<?php

namespace App\Service;

use App\Dto\GuessEvaluationPercentagesDto;
use App\Entity\Team;
use App\Entity\User;
use App\Enum\LetterEvaluationEnum;
use App\Repository\GuessRepository;
use App\Repository\TeamRepository;
use App\Repository\UserRepository;

class StatsService
{
    public function __construct(
        private readonly GameService $gameService,
        private readonly TeamRepository $teamRepository,
        private readonly GuessRepository $guessRepository,
        private readonly UserRepository $userRepository,
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

        $guesses = $this->guessRepository->findGuessesByUser($user);
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

    public function getGamesGroupedByDayForCurrentMonth(): array
    {
        $today = new \DateTime(); // Current date
        $startOfMonth = $today->modify('first day of this month')->setTime(0, 0); // Start of the current month
        $endOfMonth = (clone $startOfMonth)->modify('last day of this month')->setTime(23, 59, 59); // End of the current month

        // Fetch games created within the current month
        $gamesThisMonth = $this->gameService->getGamesBetweenDates($startOfMonth, $endOfMonth);

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
}
