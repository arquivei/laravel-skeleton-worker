<?php

declare(strict_types=1);

namespace App\Adapters\Kafka;

class KafkaConfig
{
    public function __construct(
        private string $brokers,
        private string $saslUsername,
        private string $saslPassword,
        private string $saslMechanism,
        private string $securityProtocol,
        private string $eventsStream,
    ) {
    }

    public function getBrokers(): string
    {
        return $this->brokers;
    }

    public function getSaslUsername(): string
    {
        return $this->saslUsername;
    }

    public function getSaslPassword(): string
    {
        return $this->saslPassword;
    }

    public function getSaslMechanism(): string
    {
        return $this->saslMechanism;
    }

    public function getSecurityProtocol(): string
    {
        return $this->securityProtocol;
    }

    public function getEventsStream(): string
    {
        return $this->eventsStream;
    }
}
