# PrestaShop pull request review comments statistics

## Collection

A GitHub webhooks pushes `pull_request_review` and `pull_request_review_comment` events that are recorded in `ReceiveGitHubWebHookController`.
Each webhook call is stored in MySQL.

Statistics are computed by console Command `php bin/console ps:pr-review-comment-stats:compute`.
Data come from MySQL and is enriched by GitHub REST API.

Statistics computation is done once a day, to collect data from the day before.

Webhook data older than 3 days is deleted once a day.

## Display

Last 30 days of computed statistics data is displayed on a web page.
