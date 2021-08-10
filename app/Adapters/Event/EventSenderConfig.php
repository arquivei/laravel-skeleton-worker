<?php

declare(strict_types=1);

namespace App\Adapters\Event;

use Arquivei\Events\Sender\Pusher;

class EventSenderConfig
{
    public function __construct(
        private Pusher $pusher,
        private string $eventsStream,
    ) {
    }

    public function getPusher(): Pusher
    {
        return $this->pusher;
    }

    public function getEventsStream(): string
    {
        return $this->eventsStream;
    }
}
