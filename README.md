# Laravel Skeleton Worker

Base project for Command line Workers using Laravel

## Requirements

+ PHP 8.1+
+ Composer
+ Git
+ docker-composer 1.26+

## Create a new worker

```shell script
composer create-project arquivei/laravel-skeleton-worker my-worker
```
Or

```shell script
docker run --rm -it -v $PWD:/app composer create-project arquivei/laravel-skeleton-worker my-worker
```

Edit `.env`

+ APP_IDENTIFIER=<NEW-API-NAME>
+ COMPOSER_AUTH=

```shell script
make setup

sudo chmod -r 777 storage/
```

## Usage

```shell script
php artisan start:worker
```

## About Example Producer

+ Variables taken from .env file or class initialization options
+ Will post in the topic:
  + KAFKA_PREFIX + EVENTS_STREAM 
  + com.arquivei.stonks-events

## About Example Consumer

+ Variables taken from .env file or class initialization options
+ Will consume from the queue:
  + KAFKA_PREFIX + EVENTS_STREAM + 'topic' 
  + com.arquivei.stonks-events.example-app_example-event


## Run Example Producer or Consumer

+ Edit .env with kafka information
+ With artisan run the command example:consumer then example:producer

## Run Example Integration Tests 

+ Start docker compose with zookeeper, kafka and kafdrop:
    ```shell script
    docker-compose up
    ```
* Run tests: ProducerCommandtTest.php and ExampleConsumerTest.php
* Go to http://localhost:9090 to see topics created in local kafka

