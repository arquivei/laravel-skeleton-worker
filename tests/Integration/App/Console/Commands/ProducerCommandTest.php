<?php

namespace Tests\Integration\App\Console\Commands;

use Tests\Integration\IntegrationTestCase;

class ProducerCommandTest extends IntegrationTestCase
{
    public function testShouldSucceed()
    {
        $source = $this->getEventStream();
        $prefix = "com.integration-test";

        $this->artisan("example:producer --prefix=$prefix --source=$source")
            ->expectsOutput("Pushing event to stream $prefix.$source")
            ->expectsOutput("Pushed event to stream $prefix.$source")
            ->assertExitCode(0);
    }
}
