<?php

namespace Intelogis\App\ShipmentProviders;

use DateTimeImmutable;
use Intelogis\App\JsonApiClient;
use Intelogis\App\ShipmentDetails;
use Intelogis\App\Exceptions\ShipmentException;

class SlowShipment implements ShipmentProvider
{
    private const URL = 'slow';
    private const BASE_PRICE = 150;

    public function __construct(
        private readonly JsonApiClient $client
    ) {
    }

    public function getShipmentDetails(string $sourceKladr, string $targetKladr, float $weight): ShipmentDetails
    {
        $response = $this->sendRequest($sourceKladr, $targetKladr, $weight);

        if (!key_exists('coefficient', $response) || !is_numeric($response['coefficient'])) {
            throw new ShipmentException();
        }

        if (!key_exists('date', $response) || !is_string($response['date'])) {
            throw new ShipmentException();
        }

        $coefficient = $response['coefficient'];
        $date = new DateTimeImmutable($response['date']);
        $error = $response['error'] ?? null;

        return new ShipmentDetails($coefficient * self::BASE_PRICE, $date, $error);
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
