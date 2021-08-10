<?php

declare(strict_types=1);

namespace App\Consumers\Middleware\Mapper;

interface MessageMapper
{
    /**
     * @throws CannotMapMessageException
     */
    public function map(mixed $message): mixed;
}
