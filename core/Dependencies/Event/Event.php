<?php

declare(strict_types=1);

namespace Core\Dependencies\Event;

interface Event
{
    public function getData(): array;

    public function getSource(): string;

    public function getType(): string;

    public function getDataVersion(): int;
}
