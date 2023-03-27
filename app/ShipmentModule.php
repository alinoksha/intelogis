<?php

namespace Intelogis\App;

use Intelogis\App\Exceptions\ShipmentException;
use Intelogis\App\ShipmentProviders\ShipmentProvider;
use LogicException;
use Psr\Container\ContainerInterface;

class ShipmentModule
{
    /** @var class-string<ShipmentProvider>[] */
    private array $shipmentProviders;

    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    public function calcCosts(string $from, string $to, float $weight): array
    {
        $result = [];
        foreach ($this->getShipmentProviders() as $alias => $providerClass)
        {
            $provider = $this->container->get($providerClass);
            if (!$provider instanceof ShipmentProvider) {
                throw new LogicException();
            }
            try {
                $result[$alias] = $provider->getShipmentDetails($from, $to, $weight)->toArray();
            } catch (ShipmentException) {
                $result[$alias] = null;
                // TODO: добавить логи
            }
        }

        return $result;
    }

    /**
     * @return class-string<ShipmentProvider>[]
     */
    private function getShipmentProviders(): array
    {
        if (!isset($this->shipmentProviders)) {
            $this->shipmentProviders = require_once __DIR__ . '/config/shipment_providers.php';
        }

        return $this->shipmentProviders;
    }
}
