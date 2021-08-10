<?php

namespace Tests\Unit\App\Consumers\Middleware\Mapper;

use App\Consumers\Message\ExampleEventDto;
use App\Consumers\Middleware\Mapper\CannotMapMessageException;
use App\Consumers\Middleware\Mapper\DtoMapper;
use App\Consumers\Middleware\Mapper\InvalidMessageTypeException;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class DtoMapperTest extends TestCase
{
    public function testShouldThrowExceptionWhenExpectedTypeIsSetButMessageHasDifferentTypeAttribute()
    {
        $mapper = new DtoMapper(ExampleEventDto::class, 'expected-event');

        $this->expectException(InvalidMessageTypeException::class);
        $mapper->map($this->makeMessage());
    }

    public function testShouldThrowExceptionWhenMessageUnknown()
    {
        $mapper = new DtoMapper(ExampleEventDto::class);

        $this->expectException(CannotMapMessageException::class);
        $mapper->map(null);
    }

    public function testShouldMapMessageAsExpected()
    {
        $mapper = new DtoMapper(ExampleEventDto::class);

        $dto = $mapper->map($this->makeMessage());
        $event = new ExampleEventDto($this->makeMessage());
        $this->assertEquals($event, $dto);
    }

    private function makeMessage(): array
    {
        return ["SchemaVersion" => 3, "DataVersion" => 1, "Id" => "1", "CreatedAt" => Carbon::now(),
            "Source" => "example-app", "Type" => "example-event", "Data" => ["foo" => "bar"]];
    }
}
