<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use App\Database\Entity\PRWaitingReviewStatus;
use DateTime;
use Doctrine\ORM\EntityManager;
use Exception;

class PRWaitingReviewStatusRecordService
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
     * @param bool $forAPI
     *
     * @return string
     */
    public static function getURL($forAPI = false): string
    {
        $filters = 'q=is%3Aopen+is%3Apr+org%3APrestaShop+is%3Apr+is%3Aopen+review%3Arequired+draft%3Afalse+-label%3A%22Waiting+for+author%22+';

        if ($forAPI) {
            return 'https://api.github.com/search/issues?per_page=100&' . $filters;
        } else {
            return 'https://github.com/pulls?' . $filters;
        }
    }

    /**
     * @param bool $isDryRun
     *
     * @return string[]
     */
    public function recordAllPRWaitingReviewStatus(bool $isDryRun): array
    {
        $day = new DateTime();
        $logs = [];

        // @todo handle pagination
        $list = $this->fetch(self::getURL(true));

        foreach ($list['items'] as $PR) {
            $prNumber = $PR['number'];
            $dirname = dirname($PR['repository_url']);
            $repoName = basename($dirname) . '/' . basename($PR['repository_url']);

            if ($repoName === 'PrestaShop/PrestaShop-1.6') {
                continue;
            }

            // @todo handle pagination
            $data = $this->fetch("https://api.github.com/repos/$repoName/issues/$prNumber/timeline?per_page=100");

            $latestReviewDate = null;
            $latestCommitDate = null;

            foreach ($data as $dataItem) {
                if ($dataItem['event'] === 'reviewed') {
                    if ($latestReviewDate !== null && (new DateTime($dataItem['submitted_at'])) < $latestReviewDate) {
                    } else {
                        $latestReviewDate = (new DateTime($dataItem['submitted_at']));
                    }
                }

                if (in_array($dataItem['event'], ['committed', 'head_ref_force_pushed'])) {
                    if ($dataItem['event'] === 'committed') {
                        $eventDate = $dataItem['committer']['date'];
                    } else {
                        $eventDate = $dataItem['created_at'];
                    }

                    if ($latestCommitDate !== null && (new DateTime($eventDate)) < $latestCommitDate) {
                    } else {
                        $latestCommitDate = (new DateTime($eventDate));
                    }
                }
            }

            $daySinceLastReview = null;
            if ($latestReviewDate) {
                $daySinceLastReview = (date_diff($day, $latestReviewDate)->days);
            }
            $daySinceLastCommit = null;
            if ($latestCommitDate) {
                $daySinceLastCommit = (date_diff($day, $latestCommitDate)->days);
            }

            $statRecord = new PRWaitingReviewStatus(
                (string)$prNumber,
                $PR['title'],
                $PR['html_url'],
                $PR['user']['login'],
                $repoName,
                (($daySinceLastReview !== null) ? (int)$daySinceLastReview : null),
                (($daySinceLastCommit !== null) ? (int)$daySinceLastCommit : null),
                new DateTime($PR['created_at'])
            );

            $logs[] = sprintf('Latest review for PR %s was %d day before (last commit: %d days)', $prNumber, $daySinceLastReview, $daySinceLastCommit);
            if (!$isDryRun) {
                $this->entityManager->persist($statRecord);
            }
        }

        if (!$isDryRun) {
            $this->entityManager->flush();
        }

        return $logs;
    }

    /**
     * @param string $url
     *
     * @return mixed
     *
     * @throws Exception
     */
    private function fetch(string $url)
    {
        $curlHandle = curl_init();
        curl_setopt($curlHandle, CURLOPT_URL, $url);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curlHandle, CURLOPT_VERBOSE, false);
        curl_setopt($curlHandle, CURLOPT_HTTPHEADER,
            array(
                'User-Agent: PHP Script',
                'Content-Type: application/json;charset=utf-8',
                'Authorization: bearer ' . $this->githubToken
            )
        );

        $data = json_decode(curl_exec($curlHandle), true);
        $info = curl_getinfo($curlHandle);
        $code = $info['http_code'];

        if ($code !== 200) {
            throw new Exception(sprintf('Received %s response', $code));
        }

        return $data;
    }
}
