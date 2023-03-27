<?php

namespace Intelogis\Tests;

use DateTimeImmutable;
use DI\Container;
use Intelogis\App\Clock;
use Intelogis\App\JsonApiClient;
use Intelogis\App\ShipmentModule;
use PHPUnit\Framework\TestCase;

class ShipmentModuleTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();

        $stub = $this->getMockBuilder(JsonApiClient::class)->disableOriginalConstructor()->getMock();
        $stub->method('get')->willReturnCallback(function (string $url, array $data = []): ?array {
            if ($url === 'fast') {
                return [
                    'price' => 123.55,
                    'period' => 2,
                    'error' => '',
                ];
            }
            if ($url === 'slow') {
                return [
                    'coefficient' => 5.0,
                    'date' => '2023-03-31',
                    'error' => '',
                ];
            }
            return null;
        });
        $this->container->set(JsonApiClient::class, $stub);

        $this->container->set(Clock::class, new class extends Clock {
            public function now(): DateTimeImmutable
            {
                return new DateTimeImmutable('2023-03-27 18:00:00');
            }
        });
    }

    public function testCalcCosts(): void
    {
        $module = $this->container->get(ShipmentModule::class);

        $res = $module->calcCosts('4700500005500230001', '4700500005500230012', 101);
        $expected = [
            'fast' => [
                'price' => 123.55,
                'date' => '2023-03-29',
                'error' => '',
            ],
            'slow' => [
                'price' => 750,
                'date' => '2023-03-31',
                'error' => '',
            ],
        ];

        $this->assertEquals($expected, $res);
    }
}
