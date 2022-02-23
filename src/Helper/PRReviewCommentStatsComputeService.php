<?php

declare(strict_types=1);

namespace App\Helper;

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Database\Entity\PRReviewCommentDayStat;
use App\Database\Entity\ReviewStat;
use DateTime;
use Exception;
use Doctrine\ORM\EntityManager;
use PDO;

class PRReviewCommentStatsComputeService
{
    /**
     * @var string
     */
    private string $githubToken;

    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @param string $githubToken
     * @param PDO $pdo
     * @param EntityManager $entityManager
     */
    public function __construct(string $githubToken, PDO $pdo, EntityManager $entityManager)
    {
        $this->githubToken = $githubToken;
        $this->pdo = $pdo;
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
    public function computePRReviewCommentStatistics(DateTime $day, bool $dryRun): array
    {
        $date = $day->format('Y-m-d');
        $team = TeamHelper::getTeam();
        $output = [];

        foreach ($team as $maintainer) {
            $allComments = $this->fetchComments($date, $maintainer);

            $groupedByPRs = [];
            $numberOfComments = 0;

            foreach ($allComments as $comment) {
                $prUrl = $comment['pr_url'];

                if (!array_key_exists($prUrl, $groupedByPRs)) {
                    $groupedByPRs[$prUrl] = [
                        'count' => 0,
                        'pr_html_url' => $prUrl,
                        'pr_html_alias' => sprintf(
                            '#%s/%s',
                            $comment['repo_name'],
                            (string)$comment['pr_number']
                        ),
                        'pr_api_url' => sprintf(
                            'https://api.github.com/repos/%s/pulls/%s',
                            $comment['repo_name'],
                            (string)$comment['pr_number']
                        )
                    ];
                }
                $groupedByPRs[$prUrl]['count']++;
                $numberOfComments++;
            }

            $details = [];
            foreach ($groupedByPRs as $prUrl => $prData) {
                $size = $this->fetchPRSize($prData['pr_api_url']);

                $details[] = sprintf(
                    'PR <a href="%s">%s</a>: %d comments, %d lines added, %d lines removed',
                    $prData['pr_html_url'],
                    $prData['pr_html_alias'],
                    $prData['count'],
                    $size['added'],
                    $size['removed']
                );
            }

            $output[] = sprintf('Recorded %d review comments for %s', $numberOfComments, $maintainer);
            if (!$dryRun) {
                $this->insertStat($maintainer, $numberOfComments, $details, $day);
            }
        }

        $this->entityManager->flush();

        return $output;
    }

    private function fetchComments(string $date, string $author)
    {
        $sql = sprintf(
            'SELECT pr_number, pr_url, repo_name FROM review_comment_webhook WHERE author = \'%s\' AND created_at = \'%s\'',
            $author,
            $date
        );

        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * @param string $url
     *
     * @return array
     */
    private function fetchPRSize(string $url): array
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

        if (!isset($data['additions'])) {
            throw new Exception('No total_count in JSON response');
        }
        if (!isset($data['deletions'])) {
            throw new Exception('No total_count in JSON response');
        }

        return [
            'added' => $data['additions'],
            'removed' => $data['deletions']
        ];
    }

    /**
     * @param string $login
     * @param int $numberOfComments
     * @param array $details
     * @param DateTime $date
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function insertStat(string $login, int $numberOfComments, array $details, DateTime $date): void
    {
        $stat = new PRReviewCommentDayStat($login, implode(PHP_EOL, $details), $date, $numberOfComments);

        $this->entityManager->persist($stat);
    }
}
