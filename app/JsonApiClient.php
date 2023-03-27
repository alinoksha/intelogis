<?php

namespace Intelogis\App;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class JsonApiClient
{
    public function __construct(
        private readonly Client $client
    ) {
    }

    public function get(string $url, array $data = []): ?array
    {
        $response = $this->client->get($url, [
            RequestOptions::JSON => $data,
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
