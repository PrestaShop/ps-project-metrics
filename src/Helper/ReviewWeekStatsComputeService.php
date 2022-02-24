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

class ReviewWeekStatsComputeService
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
     * @param DateTime $day
     * @param string $maintainer
     * @param bool $onlyWeekTotals
     *
     * @return array
     */
    public function computePRReviewCommentStatistics(DateTime $day, string $maintainer, bool $onlyWeekTotals = true): array
    {
        $logs = [];

        $ranges = DayComputer::getPastWeekRanges(12, $day);

        foreach ($ranges as $weekNumber => $range) {
            $reviewNumbers = $this->getReviewNumbers($maintainer, $range);

            if ($onlyWeekTotals) {
                $logs[] = array_sum($reviewNumbers);
            } else {
                $logs[] = sprintf(
                    'Week %s - review stats %s | average %s',
                    (new DateTime($range[0]))->format("W"),
                    implode(', ', $reviewNumbers),
                    array_sum($reviewNumbers)
                );
            }
        }

        return $logs;
    }

    private function getReviewNumbers(string $maintainer, array $range): array
    {
        $sql = sprintf(
            'SELECT total FROM reviews WHERE login = \'%s\' AND day BETWEEN \'%s\' AND \'%s\'',
            $maintainer,
            $range[0],
            $range[1]
        );

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }
}
