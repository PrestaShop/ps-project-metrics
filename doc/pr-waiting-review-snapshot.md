# PrestaShop "Waiting for review" for how long snapshots

## Collection

Data collection triggered by console Command `php bin/console ps:prs-statuses:record`

Data is collected from GitHub using Timeline API.

Example of query:
```
https://api.github.com/repos/prestashop/prestashop/issues/82917/timeline?per_page=100"
```

Collection is done every hour because this is a snapshot. Each data collection erases previous snapshot.

## Display

All PRs are displayed on a web page.
