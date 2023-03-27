<?php

namespace Intelogis\App;

use DateTimeImmutable;

class ShipmentDetails
{
    public function __construct(
        private readonly float $price,
        private readonly DateTimeImmutable $date,
        private readonly ?string $error = null
    ) {
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function toArray(): array
    {
        return [
            'price' => $this->price,
            'date' => $this->date->format('Y-m-d'),
            'error' => $this->error,
        ];
    }
}
