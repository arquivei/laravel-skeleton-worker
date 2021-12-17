<?php

namespace App\Adapters\Event;

use Core\Dependencies\Event\Event;

class ExampleEvent implements Event
{
    private string $source = 'example-app';
    private string $type = 'example-event';

    public function __construct(
        private array $data,
        private int $dataVersion
    ) {
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDataVersion(): int
    {
        return $this->dataVersion;
    }
}
