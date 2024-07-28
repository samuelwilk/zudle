<?php

namespace App\Twig\Components\Forms;

use App\Entity\Game;
use App\Entity\Guess;
use App\Form\GuessType;
use App\Service\GameService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\LiveComponent\ValidatableComponentTrait;

#[AsLiveComponent('Form:GuessForm')]
class GuessForm extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;
    use ComponentToolsTrait;
    use ValidatableComponentTrait;

    #[LiveProp(writable: true)]
    public array $letters = [];

    /**
     * The initial data used to create the form.
     */
    #[LiveProp]
    #[Assert\Valid()]
    public ?Game $game = null;

    public function mount(Game $game): void
    {
        $this->game = $game;
        $length = $game->getWordLength();
        $this->letters = array_fill(0, $length, null);
    }

    protected function instantiateForm(): FormInterface
    {
        if (!$this->game instanceof Game) {
            throw new \LogicException('The game must be set before creating the form.');
        }

        $length = $this->game->getWordLength();

        return $this->createForm(GuessType::class, new Guess(), [
            'length' => $length,
        ]);
    }

    #[LiveAction]
    #[LiveListener('makeGuess')]
    public function makeGuess(GameService $gameService, HubInterface $hub): Response
    {
        $this->submitForm();

        $this->validate();

        $errors = $this->form->getErrors(true, false);

        $guess = implode('', $this->letters);
        $game = $gameService->makeGuess($this->game, $guess);

        // $this->render()
        try {
            $hub->publish(new Update(
                'game_state',
                $this->renderView('game/game_state.stream.html.twig', [
                    'gameState' => $gameService->getCurrentGameState($game),
                ])
            ));

            // $hub->publish(new Update(
            //    'game_calendar',
            //    $this->renderView('dashboard/calendar/game_calendar.stream.html.twig', [
            //        'games' => $this->gameService->getGames(),
            //    ])
            // ));
        } catch (\Exception $e) {
            $this->addFlash('error', 'Failed to publish updates: '.$e->getMessage());
        }

        return $this->redirectToRoute('app_game_show', ['id' => $game->getId()]);
    }

    #[LiveListener('keyDown')]
    public function handleKeyDown(#[LiveArg] string $key): void
    {
        if ('Backspace' === $key) {
            // remove the last non-null element from the array
            $lettersReversed = array_reverse($this->letters, true);
            foreach ($lettersReversed as $index => $letter) {
                if (null !== $letter) {
                    $this->letters[$index] = null;

                    break;
                }
            }

            return;
        }
        // if the key is enter, submit the form
        if ('Enter' === $key) {
            $this->emitSelf('makeGuess');

            return;
        }
        // ensure that the letter is a single character (a-z)
        if (1 !== strlen($key) || !ctype_alpha($key) || !ctype_lower($key)) {
            return;
        }
        // push the key to the first non null letter
        foreach ($this->letters as $index => $letter) {
            if (null === $letter) {
                $this->letters[$index] = strtoupper($key);

                return;
            }
        }
    }
}
