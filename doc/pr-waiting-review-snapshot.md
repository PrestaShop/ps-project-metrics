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

At the top total numbers are displayed.

Then are displayed PRs never reviewed. Down are displayed PRs, the ones which have not been reviewed for a long time first.

Each PR provides 3 information: how long since last review, how long since last commit, is that a PR from the maintainer team.
