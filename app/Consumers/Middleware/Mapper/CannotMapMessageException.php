<?php

declare(strict_types=1);

namespace App\Consumers\Middleware\Mapper;

use InvalidArgumentException;

class CannotMapMessageException extends InvalidArgumentException
{
}
