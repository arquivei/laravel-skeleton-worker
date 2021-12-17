<?php

declare(strict_types=1);

namespace App\Consumers\Message;

use Spatie\DataTransferObject\DataTransferObject;

class ExampleDataDto extends DataTransferObject
{
    public string $foo;
}
