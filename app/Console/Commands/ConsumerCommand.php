<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Adapters\Kafka\KafkaConsumerBuilder;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

abstract class ConsumerCommand extends Command
{
    public function __construct(private KafkaConsumerBuilder $builder)
    {
        parent::__construct();

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'topic',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The topic name to be consumed',
                default: 'example-app_example-event'
            )
        );

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'eventStream',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The source stream, it should usually be either saas or events',
                default: config()->get('services.kafka.events_stream')
            )
        );

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'max-messages',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The max number of messages to be consumed',
                default: '-1'
            )
        );

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'commit-size',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The commit batch size',
                default: '1'
            )
        );

        $this->getDefinition()->addOption(
            new InputOption(
                name: 'offset-reset',
                mode: InputOption::VALUE_REQUIRED,
                description: 'The auto offset reset strategy [latest|earliest]',
                default: 'earliest'
            )
        );
    }

    protected function makeConsumerBuilder(): KafkaConsumerBuilder
    {
        $topic = strval($this->option('topic'));
        $eventStream = strval($this->option('eventStream'));
        $maxMessages = intval($this->option('max-messages'));
        $commitSize = intval($this->option('commit-size'));
        $resetStrategy = strval($this->option('offset-reset'));

        return $this->builder
            ->withOption('auto.offset.reset', $resetStrategy)
            ->withGroupId('php-kafka')
            ->withPrefix(config()->get('services.kafka.prefix'))
            ->withTopicName($topic, $eventStream)
            ->withMaxMessages($maxMessages)
            ->withCommitSize($commitSize);
    }
}
