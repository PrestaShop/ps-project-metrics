# PrestaShop Project Metrics application

[![PHPUnit tests](https://github.com/matks/ps-project-metrics/actions/workflows/phpunit.yml/badge.svg)](https://github.com/matks/ps-project-metrics/actions/workflows/phpunit.yml)
[![PHPStan](https://github.com/matks/ps-project-metrics/actions/workflows/phpstan.yml/badge.svg)](https://github.com/matks/ps-project-metrics/actions/workflows/phpstan.yml)

Symfony 5 application that collects and displays

- PrestaShop Maintainers review daily statistics
- PrestaShop "Waiting for..." daily total statistics
- PrestaShop "Waiting for review" for how long snapshots
- PrestaShop pull request review comments statistics

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

Example
```
APP_ENV=dev
APP_SECRET=abcdefhejeljdxsjshdjfrhghefjejej

DATABASE_URL="mysql://abcd:abcd@127.0.0.1:9999/review-stats?serverVersion=5.7"

APP_DB_HOST=127.0.0.1
APP_DB_TABLE=review-stats
APP_DB_USER=abcd
APP_DB_PASSWORD=abcd
APP_GH_TOKEN=ghp_abchdksjdkdjfhdjzdjdzdhazdazhduzdhzd
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
# To collect Maintainers review daily statistics ; run once a day
php bin/console ps:review-stats:record

# To collect "Waiting for..." daily total statistics ; run once a day
php bin/console ps:prs-waiting-stats:record

# To collect "Waiting for review" for how long snapshots ; can be run as often as needed
php bin/console ps:prs-statuses:record

# To collect pull request review comments statistics ; run once a day
php bin/console ps:pr-review-comment-stats:compute
```

Each command can be triggered as dry-run (does not persist data) or not. Default is dry-run enabled, so in order to
persist the data you need to add `--dry-run=false`:

```
php bin/console ps:prs-waiting-stats:record --dry-run=false
php bin/console ps:review-stats:record --dry-run=false
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

## Documentation

[Browse `/doc` folder](https://github.com/matks/ps-project-metrics/tree/master/doc)
