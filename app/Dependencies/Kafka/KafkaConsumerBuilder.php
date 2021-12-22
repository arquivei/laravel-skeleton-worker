<?php

declare(strict_types=1);

namespace App\Dependencies\Kafka;

use App\Consumers\Middleware\Mapper\MessageMapper;
use App\Consumers\Middleware\MessageDecoderMiddleware;
use Arquivei\LogAdapter\Log;
use Kafka\Consumer\Consumer;
use Kafka\Consumer\ConsumerBuilder;
use Kafka\Consumer\Entities\Config;
use RuntimeException;

class KafkaConsumerBuilder
{
    private string $prefix = '';
    private string $topicName = '';
    private int $commitSize = 1;
    private string $groupId = '';
    private ?string $dlqTopicName = null;
    private bool $hasDlq = false;
    private string $topicSource = '';
    private array $middlewares = [];
    private int $maxMessages = -1;
    private ?string $fullTopicName = null;
    private array $options = [];
    private bool $autoCommit = false;

    public function __construct(
        private Log $logger,
        private KafkaConfig $kafkaConfig,
    ) {
    }

    public function withTopicName(string $topicName, string $source = 'events'): self
    {
        if (empty($topicName)) {
            throw new RuntimeException("The kafka topic name can't be an empty string");
        }

        $builder = clone $this;
        $builder->topicName = $topicName;
        $builder->topicSource = $source;
        return $builder;
    }

    public function withPrefix(string $prefix): self
    {
        $builder = clone $this;
        $builder->prefix = $prefix;
        return $builder;
    }

    public function withCommitSize(int $commitSize): self
    {
        $builder = clone $this;
        $builder->commitSize = $commitSize;
        return $builder;
    }

    public function withGroupId(string $groupId): self
    {
        $builder = clone $this;
        $builder->groupId = $groupId;
        return $builder;
    }

    public function withDlqTopic(string $dlqTopicName = null): self
    {
        $builder = clone $this;
        $builder->hasDlq = true;
        $builder->dlqTopicName = $dlqTopicName;
        return $builder;
    }

    public function withMiddleware(callable $middleware): self
    {
        $builder = clone $this;
        $builder->middlewares[] = $middleware;
        return $builder;
    }

    public function withDecoderMiddleware(MessageMapper ...$mappers): self
    {
        return $this->withMiddleware(new MessageDecoderMiddleware($this->logger, ...$mappers));
    }

    public function withMaxMessages(int $maxMessages): self
    {
        $builder = clone $this;
        $builder->maxMessages = $maxMessages;
        return $builder;
    }

    public function withAutoCommit(): self
    {
        $builder = clone $this;
        $builder->autoCommit = true;
        return $builder;
    }

    public function withOption(string $name, string $value): self
    {
        $builder = clone $this;
        $builder->options[$name] = $value;
        return $builder;
    }

    /**
     * @param callable(mixed): void $handler
     */
    public function build(callable $handler): Consumer
    {
        if (null === $this->fullTopicName) {
            $this->fullTopicName = $this->buildTopicByTopicName($this->topicName, $this->topicSource);
        }

        $dlq = $this->buildDlqTopic();

        $this->logger->info('Building new consumer', [
            'consumer_topic' => $this->fullTopicName,
            'consumer_dlq' => $dlq,
            'consumer_group' => $this->groupId,
            'consumer_commit_size' => $this->commitSize,
            'consumer_class' => get_class($handler),
            'consumer_auto_commit' => $this->autoCommit,
            'consumer_custom_options' => (string) json_encode($this->options),
        ]);

        $consumerBuilder = ConsumerBuilder::create(
            $this->kafkaConfig->getBrokers(),
            $this->groupId,
            [$this->fullTopicName]
        )
            ->withSasl(
                new Config\Sasl(
                    $this->kafkaConfig->getSaslUsername(),
                    $this->kafkaConfig->getSaslPassword(),
                    $this->kafkaConfig->getSaslMechanism()
                )
            )
            ->withSecurityProtocol($this->kafkaConfig->getSecurityProtocol())
            ->withCommitBatchSize($this->commitSize)
            ->withDlq($dlq)
            ->withMaxMessages($this->maxMessages)
            ->withOptions($this->options)
            ->withHandler($handler);

        foreach ($this->middlewares as $middleware) {
            $consumerBuilder->withMiddleware($middleware);
        }

        if ($this->autoCommit) {
            $consumerBuilder->withAutoCommit();
        }

        return $consumerBuilder->build();
    }

    private function buildTopicByTopicName(string $topicName, string $source = 'events'): string
    {
        return "{$this->prefix}.{$source}.{$topicName}";
    }

    private function buildDlqTopic(): ?string
    {
        if (!$this->hasDlq) {
            return null;
        }

        if (null === $this->dlqTopicName) {
            return "{$this->buildTopicByTopicName($this->topicName, 'saas')}-dlq";
        }
        return $this->buildTopicByTopicName($this->dlqTopicName, 'saas');
    }
}
