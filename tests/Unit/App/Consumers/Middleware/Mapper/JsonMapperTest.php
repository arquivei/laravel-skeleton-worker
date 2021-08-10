<?php

declare(strict_types=1);

namespace Tests\Unit\App\Consumers\Middleware\Mapper;

use App\Consumers\Middleware\Mapper\CannotMapMessageException;
use App\Consumers\Middleware\Mapper\JsonMapper;
use PHPUnit\Framework\TestCase;

class JsonMapperTest extends TestCase
{
    private JsonMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new JsonMapper();
    }

    public function testShouldThrowExceptionWhenAnInvalidJsonIsProvided(): void
    {
        $this->expectException(CannotMapMessageException::class);
        $this->mapper->map('{"foo": "bar.txt"');
    }

    public function testShouldThrowExceptionWhenAStringIsNotProvided(): void
    {
        $this->expectException(CannotMapMessageException::class);
        $this->mapper->map(null);
    }

    /**
     * @testWith ["20"]
     *           ["null"]
     *           ["[42, 43]"]
     */
    public function testShouldThrowExceptionWhenTheDecodedValueIsNotAJsonObject(mixed $message): void
    {
        $this->expectException(CannotMapMessageException::class);
        $this->mapper->map($message);
    }

    public function testShouldReturnTheDecodedObjectAsAnArray(): void
    {
        $decoded = $this->mapper->map('{"answer": 42}');
        $this->assertEquals(['answer' => 42], $decoded);
    }
}
