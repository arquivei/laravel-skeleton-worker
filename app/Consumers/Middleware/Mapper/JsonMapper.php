<?php

declare(strict_types=1);

namespace App\Consumers\Middleware\Mapper;

use Illuminate\Support\Arr;
use JsonException;

class JsonMapper implements MessageMapper
{
    public function map(mixed $message): array
    {
        if (!is_string($message)) {
            throw new CannotMapMessageException(
                sprintf('The message should be string, \'%s\' given', gettype($message))
            );
        }

        $decoded = $this->decode($message);

        if (!is_array($decoded)) {
            throw new  CannotMapMessageException('The message should be a valid json object');
        }

        if (!Arr::isAssoc($decoded)) {
            throw new  CannotMapMessageException('The message should be a valid json object');
        }

        return $decoded;
    }

    private function decode(string $message): mixed
    {
        try {
            return json_decode($message, associative: true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new CannotMapMessageException(
                $exception->getMessage(),
                previous: $exception
            );
        }
    }
}
