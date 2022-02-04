# fruitdelapassion application

[![PHPUnit tests](https://github.com/matks/fruitdelapassion/actions/workflows/phpunit.yml/badge.svg)](https://github.com/matks/fruitdelapassion/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/matks/fruitdelapassion/actions/workflows/phpstan.yml/badge.svg)](https://github.com/matks/fruitdelapassion/actions/workflows/phpstan.yml)

Symfony 5 application that collects and displays

- GitHub pull request review statistics
- GitHub waiting pull request statistics

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

### Browse statistics

Browse `/` using a webserver to see the dashboard.

If you configure a webserver, do use `public` as root directory.

Example with Symfony built-in server:

```
symfony server:start
```

### Collect statistics

```
# To collect pull request statistics
php bin/console matks:prs-waiting-stats:record
# To collect pull request review statistics
php bin/console matks:review-stats:record
```

Each command can be triggered as dry-run (does not persist data) or not. Default is dry-run enabled, so in order to
persist the data you need to add `--dry-run=false`:

```
php bin/console matks:prs-waiting-stats:record --dry-run=false
php bin/console matks:review-stats:record --dry-run=false
```

## Test

Run tests using phpunit

```
vendor/bin/phpunit -c phpunit.xml
```

Some tests do load fixtures powered by a sqlite driver

Run static analysis using phpstan

```
vendor/bin/phpstan analyse -c phpstan.neon
```
