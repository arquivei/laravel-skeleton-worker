<?php

declare(strict_types=1);

namespace App\Consumers\Middleware\Mapper;

class DtoMapper implements MessageMapper
{
    public function __construct(
        private string $dtoClass,
        private ?string $expectedType = null,
    ) {
    }

    public function map(mixed $message): object
    {
        if (!is_array($message)) {
            throw new CannotMapMessageException(
                sprintf('The message should be an array, \'%s\' given', gettype($message))
            );
        }

        $dto = new $this->dtoClass($message);

        if (
            null !== $this->expectedType
            && $dto->Type !== $this->expectedType
        ) {
            throw new InvalidMessageTypeException(
                expected: $this->expectedType,
                actual: $dto->Type
            );
        }

        return $dto;
    }
}
