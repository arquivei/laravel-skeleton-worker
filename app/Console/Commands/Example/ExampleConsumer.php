<?php

namespace App\Console\Commands\Example;

use App\Console\Commands\ConsumerCommand;
use App\Consumers\Message\ExampleEventDto;
use App\Consumers\Middleware\Mapper\DtoMapper;
use App\Consumers\Middleware\Mapper\JsonMapper;

class ExampleConsumer extends ConsumerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'example:consumer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumer command example';

    public function handle(): int
    {
        $consumer = $this->makeConsumerBuilder()
            ->withDecoderMiddleware(
                new JsonMapper(),
                new DtoMapper(ExampleEventDto::class, "example-event"),
            )
            ->build($this->handleMessage());
        $consumer->consume();
        return 0;
    }

    protected function handleMessage(): callable
    {
        return function ($event) {
            $this->line("Reading message from stream {$this->option('eventStream')}");
            $this->line(json_encode($event));
            $this->line("Readed message from stream {$this->option('eventStream')}");
        };
    }
}
