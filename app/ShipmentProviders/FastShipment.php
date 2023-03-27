<?php

namespace Intelogis\App\ShipmentProviders;

use Intelogis\App\Clock;
use Intelogis\App\JsonApiClient;
use Intelogis\App\ShipmentDetails;
use Intelogis\App\Exceptions\ShipmentException;

class FastShipment implements ShipmentProvider
{
    private const URL = 'fast';
    private const NO_REQUESTS_AFTER = 18;

    public function __construct(
        private readonly JsonApiClient $client,
        private readonly Clock $clock
    ) {
    }

    public function getShipmentDetails(string $sourceKladr, string $targetKladr, float $weight): ShipmentDetails
    {
        $response = $this->sendRequest($sourceKladr, $targetKladr, $weight);

        if (!key_exists('price', $response) || !is_numeric($response['price'])) {
            throw new ShipmentException();
        }

        if (!key_exists('period', $response) || !is_int($response['period'])) {
            throw new ShipmentException();
        }

        $price = $response['price'];

        $date = $this->clock->now();

        if ((int)$date->format('H') < self::NO_REQUESTS_AFTER) {
            $date = $date->modify(sprintf('+%d day', $response['period'] - 1));
        } else {
            $date = $date->modify(sprintf('+%d day', $response['period']));
        }

        $error = $response['error'] ?? null;

        return new ShipmentDetails($price, $date, $error);
    }

    private function sendRequest(string $sourceKladr, string $targetKladr, float $weight): array
    {
        return $this->client->get(
            self::URL,
            [
                'sourceKladr' => $sourceKladr,
                'targetKladr' => $targetKladr,
                'weight' => $weight,
            ]
        );
    }
}
