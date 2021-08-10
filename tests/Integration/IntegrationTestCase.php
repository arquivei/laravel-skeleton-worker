<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\TestCase;

class IntegrationTestCase extends TestCase
{
    protected function getEventStream(): string
    {
        return uniqid() . "-" . config()->get('services.kafka.events_stream');
    }
}
