<?php

namespace Tests\Integration\App\Console\Commands\Example;

use RuntimeException;
use Tests\Integration\IntegrationTestCase;

class ExampleConsumerTest extends IntegrationTestCase
{

    public function testShouldConsumeFromEvent(): void
    {
        $eventStream = $this->getEventStream();

        $this->artisan("example:producer --source={$eventStream}.example-app_example-event")
            ->assertExitCode(0);

        $this->artisan("example:consumer
            --topic=example-app_example-event --max-messages=1 --eventStream={$eventStream}")
            ->expectsOutput("Reading message from stream $eventStream")
            ->expectsOutput("Readed message from stream $eventStream")
            ->assertExitCode(0);
    }

    public function testShouldThrowExceptionTopicNameEmpty()
    {
        $this->expectException(RuntimeException::class);
        $this->artisan("example:consumer --topic= ");
    }
}
