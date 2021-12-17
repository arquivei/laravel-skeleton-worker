<?php

declare(strict_types=1);

namespace Core\Dependencies;

interface ContextualLogger extends LogInterface
{
    public function setTraceId(?string $traceId = null): void;

    public function addContext(array $context): void;

    public function addExtra(array $extra): void;
}
