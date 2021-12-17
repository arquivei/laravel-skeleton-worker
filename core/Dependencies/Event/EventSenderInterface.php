<?php

declare(strict_types=1);

namespace Core\Dependencies\Event;

interface EventSenderInterface
{
    public function push(Event $event, string $stream): void;
}
