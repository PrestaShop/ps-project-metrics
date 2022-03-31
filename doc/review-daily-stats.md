# PrestaShop Maintainers review daily stats statistics

## Collection

Data collection triggered by console Command `php bin/console ps:review-stats:record`

Data is collected from GitHub using GraphQL API https://docs.github.com/en/graphql/overview/explorer

Example of query:
```
		{
		  user(login: "%s") {
		    contributionsCollection(from: "%s", to: "%s") {
		      pullRequestReviewContributions(first: 100) {
		        edges {
		          node {
		            occurredAt
		            pullRequest {
		              url
		            }
		          }
		        }
		      }
		    }
		  }
		}
```

Collection is done once a day, to collect data from the day before.

## Display

Data is displayed on homepage in 2 blocks.

First block is a view of the last 7 days, columns are days, rows are maintainers.

Second block is the next 23 days, columns are maintainers, rows are days.

You can click on a maintainer name to see the details of collected review data.

Badges help monitoring numbers. If a maintainer reviewed some PRs during the week-end they are displayed, else they are not (week-end are supposed to be off :D.
