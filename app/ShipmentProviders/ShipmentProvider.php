<?php

namespace Intelogis\App\ShipmentProviders;

use Intelogis\App\Exceptions\ShipmentException;
use Intelogis\App\ShipmentDetails;

interface ShipmentProvider
{
    /**
     * @throws ShipmentException
     */
    public function getShipmentDetails(string $sourceKladr, string $targetKladr, float $weight): ShipmentDetails;
}
