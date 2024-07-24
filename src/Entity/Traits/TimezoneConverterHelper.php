<?php

namespace App\Entity\Traits;

trait TimezoneConverterHelper
{
    /**
     * @throws \Exception
     */
    public function convertDateTimeToUTC(?\DateTimeImmutable $dateTimeInterface): ?\DateTimeImmutable
    {
        return $dateTimeInterface?->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @throws \Exception
     */
    public function convertDateTimeToCST(?\DateTimeImmutable $dateTimeInterface): ?\DateTimeImmutable
    {
        return $dateTimeInterface?->setTimezone(new \DateTimeZone('America/Regina'));
    }
}
