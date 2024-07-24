<?php

namespace App\Entity;

use App\Entity\Traits\TimeStampable;
use App\Enum\GameStatusEnum;
use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Broadcast]
class Game
{
    use TimeStampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $word = null;

    #[ORM\Column]
    private ?int $status = GameStatusEnum::IN_PROGRESS->value;

    #[ORM\Column(nullable: true)]
    private ?int $attempts = 0;

    #[ORM\Column]
    private ?int $maxAttempts = 5;

    /**
     * @var Collection<int, Guess>
     */
    #[ORM\OneToMany(targetEntity: Guess::class, mappedBy: 'game', cascade: ['persist'], orphanRemoval: true)]
    private Collection $guesses;

    public function __construct()
    {
        $this->guesses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWord(): ?string
    {
        return $this->word;
    }

    public function setWord(string $word): static
    {
        $this->word = $word;

        return $this;
    }

    /**
     * @return Collection<int, Guess>
     */
    public function getGuesses(): Collection
    {
        return $this->guesses;
    }

    public function addGuess(Guess $guess): static
    {
        if (!$this->guesses->contains($guess)) {
            $this->guesses->add($guess);
            $guess->setGame($this);
        }

        return $this;
    }

    public function removeGuess(Guess $guess): static
    {
        if ($this->guesses->removeElement($guess)) {
            // set the owning side to null (unless already changed)
            if ($guess->getGame() === $this) {
                $guess->setGame(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?GameStatusEnum
    {
        return GameStatusEnum::tryFrom($this->status);
    }

    public function setStatus(GameStatusEnum $gameStatusEnum): static
    {
        $this->status = $gameStatusEnum->value;

        return $this;
    }

    public function getWordLength(): int
    {
        $word = $this->getWord();

        return strlen($word);
    }

    public function getAttempts(): ?int
    {
        return $this->attempts;
    }

    public function setAttempts(int $attempts): static
    {
        $this->attempts = $attempts;

        return $this;
    }

    public function getMaxAttempts(): ?int
    {
        return $this->maxAttempts;
    }

    public function setMaxAttempts(int $maxAttempts): static
    {
        $this->maxAttempts = $maxAttempts;

        return $this;
    }

    public function incrementAttempts(): static
    {
        ++$this->attempts;

        return $this;
    }
}
