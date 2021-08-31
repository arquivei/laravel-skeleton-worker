<?php

declare(strict_types=1);

namespace App\Adapters\Monolog;

use App\Adapters\TraceId\TraceIdGenerator;
use Core\Dependencies\ContextualLogger;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Processor\UidProcessor;
use Monolog\Formatter\JsonFormatter;

class MonologLogAdapter implements ContextualLogger
{
    private Logger $logger;
    private ContextProcessor $contextProcessor;

    public function __construct(TraceIdGenerator $traceIdGenerator)
    {
        $this->contextProcessor = new ContextProcessor();
        $this->setTraceId($traceIdGenerator->generate());

        $levelDebug = env('APP_DEBUG', false);
        $handler = new StreamHandler("php://stdout", $levelDebug ? Logger::DEBUG : Logger::INFO);
        $handler->setFormatter(new JsonFormatter(ignoreEmptyContextAndExtra: true));
        $this->logger = (new Logger('api_log'))
            ->pushHandler($handler)
            ->pushProcessor(new UidProcessor(32))
            ->pushProcessor(new DatetimeProcessor())
            ->pushProcessor($this->contextProcessor);
    }

    public function setTraceId(?string $traceId = null): void
    {
        if (is_null($traceId)) {
            return;
        }

        $this->contextProcessor->addContext([
            'trace_id' => $traceId,
        ]);
    }

    public function addExtra(array $extra): void
    {
        $this->contextProcessor->addExtra($extra);
    }

    public function addContext(array $context): void
    {
        $this->contextProcessor->addContext($context);
    }

    public function resetLogger(): void
    {
        $this->logger->reset();
    }

    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
