<?php

namespace App\Entity;

use App\Dto\EvaluatedGuess;
use App\Entity\Traits\TimeStampable;
use App\Repository\GuessRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;
use App\Validator\Constraints as zuAssert;

#[ORM\Entity(repositoryClass: GuessRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Broadcast]
class Guess
{
    use TimeStampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[zuAssert\ValidEnglishWord]
    private ?string $guess = null;

    #[ORM\ManyToOne(inversedBy: 'guesses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    private ?EvaluatedGuess $evaluation = null;

    #[ORM\ManyToOne(inversedBy: 'guesses')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuess(): ?string
    {
        return $this->guess;
    }

    public function setGuess(?string $guess): static
    {
        $this->guess = $guess;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getLetters(): array
    {
        return str_split($this->guess);
    }

    public function setEvaluation(EvaluatedGuess $evaluatedGuess): static
    {
        $this->evaluation = $evaluatedGuess;

        return $this;
    }

    public function getEvaluation(): ?EvaluatedGuess
    {
        return $this->evaluation;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getGuess() ?? '';
    }
}
