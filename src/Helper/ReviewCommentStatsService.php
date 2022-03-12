<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use PDO;

class ReviewCommentStatsService
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
     * @param DateTime $from
     * @param DateTime $to
     *
     * @return array<string, mixed>
     */
    public function getTeamStatsGroupedByDay(DateTime $from, DateTime $to): array
    {
        $teamMembers = TeamHelper::getTeam();

        $sql = sprintf('SELECT login, day, total FROM review_comment_daily_stat
WHERE login IN (%s)
AND day BETWEEN \'%s\' AND \'%s\'
ORDER BY day DESC',
            '"' . implode('","', $teamMembers) . '"',
            $from->format('Y-m-d'),
            $to->format('Y-m-d')
        );

        $sqlResult = $this->pdo->query($sql)->fetchAll();

        $dateRange = DayComputer::buildArrayOfDatesFromTo($from, $to);
        $resultToBuild = array_fill_keys($dateRange, []);
        foreach ($resultToBuild as $key => $itemToBuild) {
            $resultToBuild[$key] = array_fill_keys(TeamHelper::getTeam(), 'no_data');
        }

        foreach ($sqlResult as $item) {
            $itemDay = $item['day'];
            $itemLogin = $item['login'];

            $resultToBuild[$itemDay][$itemLogin] = (int)$item['total'];
        }

        foreach ($resultToBuild as $day => $group) {
            $resultToBuild[$day] = TeamHelper::reorderByTeamOrder($group);
        }

        return array_reverse($resultToBuild);
    }

    /**
     * @param string $login
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDeveloperStats(string $login): array
    {
        $today = new DateTime();
        $threeMonthBefore = DayComputer::getXDayBefore(90, $today);

        $sql = sprintf(
            'SELECT day, details, total FROM review_comment_daily_stat WHERE login = \'%s\' AND day BETWEEN \'%s\' AND \'%s\'
ORDER BY day DESC', $login, $threeMonthBefore->format('Y-m-d'), $today->format('Y-m-d'));

        $result = $this->pdo->query($sql)->fetchAll();

        return $result;
    }
}
