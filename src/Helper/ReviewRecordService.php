<?php

declare(strict_types=1);

namespace App\Helper;

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Database\Entity\ReviewStat;
use DateTime;
use Exception;
use Doctrine\ORM\EntityManager;

class ReviewRecordService
{
    /**
     * @var string
     */
    private string $githubToken;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @param string $githubToken
     * @param EntityManager $entityManager
     */
    public function __construct(string $githubToken, EntityManager $entityManager)
    {
        $this->githubToken = $githubToken;
        $this->entityManager = $entityManager;
    }

    /**
     * @param DateTime $day
     * @param bool $dryRun
     *
     * @return string[]
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function recordReviewsForDay(DateTime $day, bool $dryRun): array
    {
        $team = TeamHelper::getTeam();
        $date = $day->format('Y-m-d');

        $output = [];

        foreach ($team as $login) {
            $dataFromAPI = json_decode(
                $this->getReviewsByDay(
                    $this->githubToken,
                    $login,
                    $date . "T00:00:00",
                    $date . "T23:59:59"
                ),
                true
            );

            $urls = $this->extractPRUrls($dataFromAPI, $login);

            $output[] = sprintf(
                '%s reviewed %d reviews on %s',
                $login,
                $urls['nb'],
                $date
            );
            if (!$dryRun) {
                $this->insertReview($login, $urls, $date);
            }
        }

        $this->entityManager->flush();

        return $output;
    }

    /**
     * @param string $login
     * @param array $PRs
     * @param string $date
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function insertReview(string $login, array $PRs, string $date): void
    {
        // If we redo the stats we need to remove previous records from the day
        $existingReviewStat = $this->entityManager->getRepository(ReviewStat::class)->findOneBy([
            'login' => $login,
            'day' => new DateTime($date),
        ]);


        if ($existingReviewStat !== null) {
            $this->entityManager->remove($existingReviewStat);
            $this->entityManager->flush();
        }

        $reviewStatRecord = new ReviewStat(
            $login,
            '"' . implode('";"', array_merge($PRs['peers'], $PRs['community'])) . '"',
            new DateTime($date),
            count($PRs['peers']),
            count($PRs['community']),
        );

        $this->entityManager->persist($reviewStatRecord);
    }

    /**
     * @param string $token
     * @param string $login
     * @param string $from
     * @param string $to
     *
     * @return string
     *
     * @see https://docs.github.com/en/graphql/overview/explorer
     */
    private function getReviewsByDay(string $token, string $login, string $from, string $to): string
    {
        $query = sprintf('
		{
		  user(login: "%s") {
		    contributionsCollection(from: "%s", to: "%s") {
		      pullRequestReviewContributions(first: 100) {
		        edges {
		          node {
		            occurredAt
		            pullRequest {
		              url,
                      author {
                        login
                      }
		            }
		          }
		        }
		      }
		    }
		  }
		}
		', $login, $from, $to);
        $json = json_encode(['query' => $query, 'variables' => []]);

        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, 'https://api.github.com/graphql');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $json);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
                'Authorization: bearer ' . $token
            )
        );

        $data = curl_exec($curlHandle);

        $info = curl_getinfo($curlHandle);
        $code = $info['http_code'];

        if ($code !== 200) {
            throw new Exception(sprintf('Received %s response', $code));
        }

        return $data;
    }

    /**
     * @param array<string, mixed> $dataFromAPI
     *
     * @return array
     */
    private function extractPRUrls(array $dataFromAPI, string $reviewer): array
    {
        $edges = reset($dataFromAPI['data']['user']['contributionsCollection']);
        $edge = reset($edges);

        $urls = [
            'nb' => 0,
            'peers' => [],
            'community' => [],
        ];
        foreach ($edge as $dataBag) {
            $PR = $dataBag['node']['pullRequest'];
            $url = $PR['url'];

            // Skipping %s because it was a self-review
            if ($PR['author']['login'] === $reviewer) {
                continue;
            }

            if (in_array($PR['author']['login'], TeamHelper::getTeam(), true)) {
                $urls['peers'][] = $url;
            } else {
                $urls['community'][] = $url;
            }

            $urls['nb']++;
        }

        return $urls;
    }
}
