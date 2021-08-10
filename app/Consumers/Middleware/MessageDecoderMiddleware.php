<?php

declare(strict_types=1);

namespace App\Consumers\Middleware;

use App\Consumers\Middleware\Mapper\CannotMapMessageException;
use App\Consumers\Middleware\Mapper\MessageMapper;
use Core\Dependencies\LogInterface;
use Illuminate\Support\Arr;
use InvalidArgumentException;
use JsonException;
use stdClass;

class MessageDecoderMiddleware
{
    private LogInterface $logger;
    private array $mappers;

    public function __construct(
        LogInterface $logger,
        MessageMapper ...$mappers
    ) {
        $this->logger = $logger;
        $this->mappers = $mappers;
    }

    public function __invoke(mixed $message, callable $next): void
    {
        if (!is_string($message)) {
            throw new InvalidArgumentException("Can only decode raw messages");
        }

        $rawMessage = $message;

        try {
            foreach ($this->mappers as $mapper) {
                $message = $mapper->map($message);
            }
        } catch (CannotMapMessageException $exception) {
            $this->logger->warning('Could not decode message', [
                'raw_message' => base64_encode($rawMessage),
                'exception' => $exception,
            ]);
            return;
        }

        $next($message);
    }
}
