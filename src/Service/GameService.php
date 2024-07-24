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
     * @return Game[]
     */
    public function getGames(): array
    {
        return $this->gameRepository->findAll();
    }

    public function getGamesGroupedByDayForCurrentMonth(): array
    {
        $gamesThisMonth = $this->gameRepository->findGamesCreatedThisMonth();
        $groupedGames = [];

        foreach ($gamesThisMonth as $game) {
            $dayOfMonth = $game->getCreatedAt()->format('j');
            if (!isset($groupedGames[$dayOfMonth])) {
                $groupedGames[$dayOfMonth] = [];
            }
            $groupedGames[$dayOfMonth][] = $game;
        }

        return $groupedGames;
    }

    public function getGamesBetweenDates(\DateTime $start, \DateTime $end): array
    {
        return $this->gameRepository->findByCreationDateRange($start, $end);
    }

    private function updateGameStatus(Game $game, string $guessWord): void
    {
        // Implement the logic to update the game's status based on the guess
        if ($game->getWord() === $guessWord) {
            $game->setStatus(GameStatusEnum::WON);
        }

        // Additional logic to update the game based on the guess could be added here
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
}
