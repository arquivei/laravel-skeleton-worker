<?php

declare(strict_types=1);

namespace App\Consumers\Middleware\Mapper;

use Throwable;

class InvalidMessageTypeException extends CannotMapMessageException
{
    public function __construct(string $expected, string $actual, Throwable $previous = null)
    {
        $message = sprintf(
            'The message type should be \'%s\' but received \'%s\'',
            $expected,
            $actual
        );

        parent::__construct($message, previous: $previous);
    }
}
