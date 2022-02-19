<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use PDO;

class PRWaitingReviewStatusListingService
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @param PDO $pdo
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return array<string, mixed>
     */
    public function getListingOfPRsWaitingForReview(): array
    {
        $sql = 'SELECT pr_number, name, url, author, repo_name, day_since_last_review, day_since_last_commit, created_at FROM pr_waiting_review_status ORDER BY day_since_last_review DESC';
        $result = $this->pdo->query($sql)->fetchAll();

        $neverReviewed = [];
        $groupedByLastReviewDate = [];

        foreach ($result as $item) {

            if (in_array($item['author'], TeamHelper::getTeam())) {
                $item['team'] = true;
            } else {
                $item['team'] = false;
            }

            if ($item['day_since_last_review'] === null) {
                $neverReviewed[] = $item;
            } else {
                $groupedByLastReviewDate = $this->addOrInsert(
                    $groupedByLastReviewDate,
                    $item['day_since_last_review'],
                    $item
                );
            }
        }

        $stats = $this->computeDayStats($groupedByLastReviewDate);
        $stats['never_reviewed'] = count($neverReviewed);

        return [
            'never_reviewed' => $neverReviewed,
            'reviewed_at_least_once' => $groupedByLastReviewDate,
            'stats' => $stats,
        ];
    }

    /**
     *
     * @param array $groupedByLastReviewDate
     * @param int $day
     * @param array $dataRow
     *
     * @return array<string, array<string, int>>
     */
    private function addOrInsert(array $groupedByLastReviewDate, int $day, array $dataRow): array
    {
        if (!array_key_exists($day, $groupedByLastReviewDate)) {
            $groupedByLastReviewDate[$day] = [];
        }
        $groupedByLastReviewDate[$day][] = $dataRow;

        return $groupedByLastReviewDate;
    }

    /**
     *
     * @param array<string, array<string, int>> $groupedByLastReviewDate
     *
     * @return array<string, int>
     */
    private function computeDayStats(array $groupedByLastReviewDate): array
    {
        $stats = [
            'less_than_4' => 0,
            'less_than_10' => 0,
            'less_than_20' => 0,
            'less_than_30' => 0,
            'less_than_60' => 0,
            'less_than_180' => 0,
            'more_than_180' => 0
        ];
        $today = new DateTime();

        foreach($groupedByLastReviewDate as $diff => $PRs) {
            if ($diff >= 0 && $diff <= 4) {
                $stats['less_than_4']++;
            } elseif ($diff > 4 && $diff <= 10) {
                $stats['less_than_10']++;
            } elseif ($diff > 10 && $diff <= 20) {
                $stats['less_than_20']++;
            } elseif ($diff > 20 && $diff <= 30) {
                $stats['less_than_30']++;
            } elseif ($diff > 30 && $diff <= 60) {
                $stats['less_than_60']++;
            } elseif ($diff > 60 && $diff <= 180) {
                $stats['less_than_180']++;
            } else {
                $stats['more_than_180']++;
            }
        }

        return $stats;
    }
}
