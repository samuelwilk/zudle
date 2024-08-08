<?php

namespace App\Service;

use App\Entity\Guess;
use App\Entity\User;
use App\Repository\GuessRepository;

class GuessService
{
    public function __construct(private readonly GuessRepository $guessRepository)
    {
    }

    public function getGuessesBetweenDates(\DateTime $start, \DateTime $end): array
    {
        return $this->guessRepository->findByCreationDateRange($start, $end);
    }

    public function countGuessesBetweenDates(?\DateTime $start, ?\DateTime $end): int
    {
        if (is_null($start) && is_null($end)) {
            return $this->guessRepository->count([]);
        }

        $games = $this->guessRepository->findByCreationDateRange($start, $end);

        return count($games);
    }

    /**
     * @return Guess[]
     */
    public function findGuessesByUser(User $user): array
    {
        return $this->guessRepository->findGuessesByUser($user);
    }
}
