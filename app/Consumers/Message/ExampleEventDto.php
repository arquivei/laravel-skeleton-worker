<?php

declare(strict_types=1);

namespace App\Consumers\Message;

use Spatie\DataTransferObject\DataTransferObject;

class ExampleEventDto extends DataTransferObject
{
    public int $SchemaVersion;
    public string $Id;
    public string $CreatedAt;
    public string $Source;
    public string $Type;
    public int $DataVersion;
    public ExampleDataDto $Data;
}
