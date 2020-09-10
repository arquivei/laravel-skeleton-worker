# Laravel Skeleton Worker

Base project for Command line Workers using Laravel

## Requirements

+ PHP 7.4+
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
