# fruitdelapassion application

[![PHPUnit tests](https://github.com/matks/fruitdelapassion/actions/workflows/phpunit.yml/badge.svg)](https://github.com/matks/fruitdelapassion/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/matks/fruitdelapassion/actions/workflows/phpstan.yml/badge.svg)](https://github.com/matks/fruitdelapassion/actions/workflows/phpstan.yml)


Symfony 5 application that collects and displays GitHub pull request review statistics.

## Install

```
composer install
```

You need

- a MySQL database
- a GitHub token with public access

You need to provide 8 parameters using a `.env.local` file or environment variables.

```
APP_ENV=dev
APP_SECRET=...

DATABASE_URL=...

APP_DB_HOST=127.0.0.1
APP_DB_TABLE=reviewstats
APP_DB_USER=...
APP_DB_PASSWORD=...
APP_GH_TOKEN=...
```

## Usage

Browse `/` using a webserver to see the dashboard.

Example:

```
symfony server:start
```

Run command to collect data of previous worked day

```
php bin/console matks:record
```

The command can be run as dry-run

```
php bin/console matks:record --dry-run=true
```

## Test

Run tests using phpunit

```
vendor/bin/phpunit -c phpunit.xml
```

Some tests do load fixtures powered by a sqlite driver
