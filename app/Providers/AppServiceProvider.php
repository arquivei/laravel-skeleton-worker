<?php

declare(strict_types=1);

namespace App\Providers;

use App\Dependencies\Event\Adapters\EventSenderAdapter;
use App\Dependencies\Event\EventSenderConfig;
use App\Dependencies\Kafka\KafkaConfig;
use Arquivei\Events\Sender\Exporters\Kafka;
use Arquivei\Events\Sender\Pusher;
use Arquivei\LogAdapter\Log;
use Arquivei\LogAdapter\LogAdapter;
use Core\Dependencies\Event\EventSenderInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     * @throws BindingResolutionException
     */
    public function register()
    {
        $this->app->singleton(Log::class, LogAdapter::class);

        $this->app->bind(KafkaConfig::class, function (): KafkaConfig {
            $config = config('services');

            return new KafkaConfig(
                brokers: $config['kafka']['broker'],
                saslUsername: $config['kafka']['sasl']['username'],
                saslPassword: $config['kafka']['sasl']['password'],
                saslMechanism: $config['kafka']['sasl']['mechanisms'],
                securityProtocol: $config['kafka']['security_protocol'],
                eventsStream: $config['kafka']['events_stream'],
                prefix: $config['kafka']['prefix'],
            );
        });

        $this->app->bind(EventSenderConfig::class, function (Application $app): EventSenderConfig {
            $kafkaConfig = $app->make(KafkaConfig::class);

            $kafkaExporter = new Kafka(config: [
                'group_id' => null,
                'kafka_brokers' => $kafkaConfig->getBrokers(),
                'security_protocol' => $kafkaConfig->getSecurityProtocol(),
                'sasl_mechanisms' => $kafkaConfig->getSaslMechanism(),
                'sasl_username' => $kafkaConfig->getSaslUsername(),
                'sasl_password' => $kafkaConfig->getSaslPassword(),
            ]);

            return new EventSenderConfig(
                pusher: new Pusher($kafkaExporter),
                eventsStream: $kafkaConfig->getEventsStream(),
            );
        });

        $this->app->bind(EventSenderInterface::class, EventSenderAdapter::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
