<?php

declare(strict_types=1);

namespace App\Adapters\Monolog;

use Monolog\Processor\ProcessorInterface;

class DatetimeProcessor implements ProcessorInterface
{
    public function __construct(private string $format = 'Y-m-d H:i:s.u')
    {
    }

    public function __invoke(array $record): array
    {
        $record['datetime'] = $record['datetime']->format($this->format);
        return $record;
    }
}
