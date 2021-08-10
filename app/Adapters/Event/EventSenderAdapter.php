<?php

declare(strict_types=1);

namespace App\Adapters\Event;

use Arquivei\Events\Sender\Exceptions\EmptyExportersException;
use Arquivei\Events\Sender\Exceptions\PusherException;
use Arquivei\Events\Sender\Factories\LatestSchemaFactory;
use Arquivei\Events\Sender\Pusher;
use Arquivei\Events\Sender\Schemas\BaseSchema;
use Core\Dependencies\Event\Event;
use Core\Dependencies\Event\EventSenderInterface;

class EventSenderAdapter implements EventSenderInterface
{
    private Pusher $pusher;
    private string $stream;

    public function __construct(
        EventSenderConfig $config,
        private LatestSchemaFactory $latestSchemaFactory,
    ) {
        $this->pusher = $config->getPusher();
        $this->stream = $config->getEventsStream();
    }

    /**
     * @throws EmptyExportersException|PusherException
     */
    public function push(Event $event, string $stream = null, string $key = null): void
    {
        if (is_null($stream)) {
            $stream = $this->stream;
        }
        $schema = $this->buildSchema($event);
        $this->pusher->push($schema, $stream, $key);
    }

    private function buildSchema(Event $event): BaseSchema
    {
        return $this->latestSchemaFactory->createFromParameters(
            $event->getSource(),
            $event->getType(),
            $event->getDataVersion(),
            $event->getData(),
        );
    }
}
