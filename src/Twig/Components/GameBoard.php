<?php

namespace App\Twig\Components;

use App\Dto\EvaluatedGuess;
use App\Dto\EvaluatedKeyboard;
use App\Dto\GameState;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use App\Entity\Game as GameEntity;

#[AsLiveComponent]
class GameBoard
{
    use DefaultActionTrait;

    // TODO: look into using serializer: https://symfony.com/bundles/ux-live-component/current/index.html#hydrating-with-the-serializer
    #[LiveProp(hydrateWith: 'hydrateGameState', dehydrateWith: 'dehydrateGameState')]
    public GameState $gameState;

    /**
     * @param GameState $gameState
     * @return array{game: GameEntity, attemptsLeft: int, evaluatedGuesses: EvaluatedGuess[], evaluatedKeyboard: EvaluatedKeyboard[]}
     */
    public function dehydrateGameState(GameState $gameState): array
    {
        return [
            'game' => $gameState->getGame(),
            'attemptsLeft' => $gameState->getAttemptsLeft(),
            'evaluatedGuesses' => $gameState->getEvaluatedGuesses(),
            'evaluatedKeyboard' => $gameState->getEvaluatedKeyboard(),
        ];
    }

    /**
     * @param array{game: GameEntity, attemptsLeft: int, evaluatedGuesses: EvaluatedGuess[], evaluatedKeyboard: EvaluatedKeyboard[]} $data
     */
    public function hydrateGameState(array $data): GameState
    {
        return new GameState($data['game'], $data['attemptsLeft'], $data['evaluatedGuesses'], $data['evaluatedKeyboard']);
    }
}
