# PrestaShop "Waiting for..." daily total stats

## Collection

Data collection triggered by console Command `php bin/console ps:prs-waiting-stats:record`

Data is collected from GitHub using REST API

Example of query:
```
https://github.com/pulls?&q=org%3APrestaShop+is%3Apr+is%3Aopen+review%3Arequired+archived%3Afalse
```

Collection is done once a day, to collect data from the day before.

## Display

Last 30 days of data is displayed on a web page.
