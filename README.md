# fruitdelapassion application

Symfony 4 application that collects review statistics collected.

## Install

```
composer install
```

You need to provide 7 parameters using a `.env` file or environment variables.

```
APP_ENV=dev
APP_SECRET=...

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

Run command to collect data of previous day
```
php bin/console matks:record
```

The command can be run as dry-run

```
php bin/console matks:record --dry-run=true
```