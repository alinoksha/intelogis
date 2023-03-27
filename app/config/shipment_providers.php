<?php

use Intelogis\App\ShipmentProviders\FastShipment;
use Intelogis\App\ShipmentProviders\SlowShipment;

return [
    'fast' => FastShipment::class,
    'slow' => SlowShipment::class,
];
