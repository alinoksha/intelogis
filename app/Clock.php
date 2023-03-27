<?php

namespace Intelogis\App;

use DateTimeImmutable;

class Clock
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
