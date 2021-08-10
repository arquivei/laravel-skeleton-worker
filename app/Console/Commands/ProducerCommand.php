<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Adapters\Event\ExampleEvent;
use Core\Dependencies\Event\Event;
use Core\Dependencies\Event\EventSenderInterface;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class ProducerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'example:producer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Producer command example';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(private EventSenderInterface $eventSender)
    {
        parent::__construct();

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'prefix',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The topic prefix',
                default: config()->get('services.kafka.prefix')
            )
        );

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'source',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The source stream, it should usually be either saas or events',
                default: config()->get('services.kafka.events_stream')
            )
        );
    }

    public function handle(): int
    {
        $events = [
            new ExampleEvent(data: ["foo" => "bar"], dataVersion: 1),
        ];

        $this->withProgressBar($events, $this->pushEvent());
        return 0;
    }

    protected function pushEvent(): callable
    {
        return function (Event $event) {
            $this->line("Pushing event to stream {$this->getStream()}");
            $this->eventSender->push($event, $this->getStream());
            $this->line("Pushed event to stream {$this->getStream()}");
        };
    }

    protected function getStream(): string
    {
        $source = $this->option('source');
        $prefix = $this->option('prefix');
        return "$prefix.$source";
    }
}
