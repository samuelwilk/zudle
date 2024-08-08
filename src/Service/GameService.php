<?php

namespace App\Service;

use App\Dto\EvaluatedGuess;
use App\Dto\EvaluatedKeyboard;
use App\Dto\GameState;
use App\Dto\LetterEvaluation;
use App\Entity\Game;
use App\Entity\Guess;
use App\Enum\GameStatusEnum;
use App\Enum\LetterEvaluationEnum;
use App\Enum\StatsChartTimeFrameEnum;
use App\Repository\GameRepository;
use Symfony\Bundle\SecurityBundle\Security;

class GameService
{
    public function __construct(private readonly GameRepository $gameRepository, private readonly Security $security)
    {
    }

    /**
     * @throws \Exception
     */
    public function makeGuess(Game $game, string $guessWord): Game
    {
        // check if the game can proceed
        if (false === $this->canMakeGuess($game)) {
            return $game;
        }

        $guess = new Guess();
        $guess->setGuess($guessWord);
        $guess->setUser($this->security->getUser());

        $game->addGuess($guess);
        $game->incrementAttempts();
        if (strtoupper($guessWord) === strtoupper($game->getWord())) {
            $game->setStatus(GameStatusEnum::WON);
        } else {
            if ($game->getAttempts() >= $game->getMaxAttempts()) {
                $game->setStatus(GameStatusEnum::LOST);
            }
        }

        $this->gameRepository->update($game, true);

        return $game;
    }

    /**
     * @return EvaluatedGuess[]
     */
    public function evaluateGuesses(array $guesses, Game $game): array
    {
        $evaluatedGuesses = [];
        foreach ($guesses as $guess) {
            $guessLetters = str_split(strtoupper($guess->getGuess()));
            $correctLetters = str_split(strtoupper($game->getWord()));
            $letterEvaluations = [];

            foreach ($guessLetters as $index => $letter) {
                if ($letter === $correctLetters[$index]) {
                    $letterEvaluations[] = new LetterEvaluation($letter, LetterEvaluationEnum::CORRECT);
                    $correctLetters[$index] = null;
                } elseif (in_array($letter, $correctLetters)) {
                    $correctIndex = array_search($letter, $correctLetters);
                    $correctLetters[$correctIndex] = null;
                    $letterEvaluations[] = new LetterEvaluation($letter, LetterEvaluationEnum::PRESENT);
                } else {
                    $letterEvaluations[] = new LetterEvaluation($letter, LetterEvaluationEnum::ABSENT);
                }
            }

            $evaluatedGuesses[] = new EvaluatedGuess($guess->getGuess(), $letterEvaluations);
        }

        return $evaluatedGuesses;
    }

    /**
     * @throws \Exception
     */
    public function getCurrentGameState(Game $game): GameState
    {
        $evaluatedGuesses = $this->evaluateGuesses($game->getGuesses()->toArray(), $game);
        $evaluatedKeyboard = new EvaluatedKeyboard($evaluatedGuesses);

        return new GameState(
            $game,
            $game->getMaxAttempts() - $game->getAttempts(),
            $evaluatedGuesses,
            $evaluatedKeyboard->getEvaluatedKeyboard()
        );
    }

    public function canMakeGuess(Game $game): bool
    {
        if (!$this->security->isGranted('ROLE_USER')) {
            return false;
        }

        $isStatusValid = GameStatusEnum::IN_PROGRESS === $game->getStatus();
        $isAttemptsValid = $game->getAttempts() < $game->getMaxAttempts();

        return $isStatusValid && $isAttemptsValid;
    }

    public function getGamesBetweenDates(\DateTime $start, \DateTime $end): array
    {
        return $this->gameRepository->findByCreationDateRange($start, $end);
    }

    public function countGamesBetweenDates(?\DateTime $start, ?\DateTime $end): int
    {
        if (is_null($start) && is_null($end)) {
            return $this->gameRepository->count([]);
        }

        $games = $this->gameRepository->findByCreationDateRange($start, $end);

        return count($games);
    }

    /**
     * TODO: move the query to the repository.
     */
    public function getGamesByStatus(GameStatusEnum $gameStatusEnum): array
    {
        $qb = $this->gameRepository->createQueryBuilder('game')
            ->andWhere('game.status = :status')
            ->setParameter('status', $gameStatusEnum->value);

        return $qb->getQuery()->getResult();
    }

    /**
     * TODO: move the query to the repository.
     */
    public function getGameStatusCounts(\DateTime $start, \DateTime $end): array
    {
        $qb = $this->gameRepository->createQueryBuilder('game')
            ->select('game.status, COUNT(game.id) as count')
            ->where('game.createdAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->groupBy('game.status');

        $result = $qb->getQuery()->getResult();

        $statusCounts = [];
        foreach ($result as $row) {
            $statusCounts[$row['status']] = $row['count'];
        }

        return $statusCounts;
    }
}
