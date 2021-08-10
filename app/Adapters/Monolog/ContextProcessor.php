<?php

declare(strict_types=1);

namespace App\Adapters\Monolog;

use Monolog\Processor\ProcessorInterface;

class ContextProcessor implements ProcessorInterface
{
    private array $extraData = [];
    private array $contextData = [];

    public function __invoke(array $record): array
    {
        foreach ($this->extraData as $key => $value) {
            $record['extra'][$key] = $value;
        }

        foreach ($this->contextData as $key => $value) {
            $record['context'][$key] = $value;
        }

        if (isset($record['context']) && empty($record['context'])) {
            unset($record['context']);
        }

        if (isset($record['extra']) && empty($record['extra'])) {
            unset($record['extra']);
        }

        return $record;
    }

    public function addContext(array $context): void
    {
        $this->contextData = array_merge($this->contextData, $context);
    }

    public function addExtra(array $extra): void
    {
        $this->extraData = array_merge($this->extraData, $extra);
    }
}
