<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;
use PDO;

class ReviewStatsService
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
    public function getTeamStatsGroupedByLogin(DateTime $from, DateTime $to): array
    {
        $teamMembers = TeamHelper::getTeam();

        $sql = sprintf('SELECT login, day, (total_peers + total_community) as total FROM reviews
            WHERE login IN (%s)
            AND day BETWEEN \'%s\' AND \'%s\'
            ORDER BY day DESC',
            '"' . implode('","', $teamMembers) . '"',
            $from->format('Y-m-d'),
            $to->format('Y-m-d')
        );

        $sqlResult = $this->pdo->query($sql)->fetchAll();

        $dateRange = DayComputer::buildArrayOfDatesFromTo($from, $to);

        $resultToBuild = TeamHelper::getTeam(true);
        foreach ($resultToBuild as $key => $itemToBuild) {
            $resultToBuild[$key] = array_fill_keys($dateRange, 'no_data');
        }

        $total = 0;
        foreach ($sqlResult as $item) {
            $itemDay = $item['day'];
            $itemLogin = $item['login'];

            $resultToBuild[$itemLogin][$itemDay] = (int)$item['total'];
            $total += (int)$item['total'];
        }

        $resultToBuild = $this->computeAndInsertTotals($resultToBuild);

        return [
            'days' => $dateRange,
            'lastSeven' => $resultToBuild,
            'totalTeam' => $total,
        ];
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

        $sql = sprintf('SELECT login, day, (total_peers + total_community) as total FROM reviews
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
     * @param int $howManyDays
     * @param DateTime $endDate
     *
     * @return array
     */
    public function getDeveloperStats(string $login, int $howManyDays, DateTime $endDate): array
    {
        $beginDate = DayComputer::getXDayBefore($howManyDays, $endDate);

        $sql = sprintf(
            'SELECT day, PR, total_peers, total_community, (total_peers + total_community) as total FROM reviews WHERE login = \'%s\' AND day BETWEEN \'%s\' AND \'%s\'
ORDER BY day DESC', $login, $beginDate->format('Y-m-d'), $endDate->format('Y-m-d'));

        $result = $this->pdo->query($sql)->fetchAll();

        $weekRanges = DayComputer::getPastWeekRanges(12, DayComputer::getSundayBefore($endDate));
        $weekRangesTotals = [];
        foreach ($weekRanges as $weekRange) {
            $weekRangesTotals[] = [
                'begin' => new DateTime($weekRange[0]),
                'end' => new DateTime($weekRange[1]),
                'number' => DayComputer::findWeekNumber(new DateTime($weekRange[0])),
                'total' => 0,
                'total_peers' => 0,
                'total_community' => 0,
            ];
        }

        $dayByDayStats = [];
        foreach ($result as $item) {
            $dayByDayStats[] = $this->buildDayStat($item);
            $weekRangesTotals = $this->insertIntoWeekStat($item, $weekRangesTotals);
        }

        return [
            'dayByDayStats' => $dayByDayStats,
            'weekStats' => $weekRangesTotals,
        ];
    }

    /**
     * @param string $PRsString
     *
     * @return string
     */
    private function formatPRs(string $PRsString): string
    {
        if ($PRsString === '""') {
            return '';
        }
        $html = '';

        $isFirst = true;
        $items = explode(';', $PRsString);

        foreach ($items as $PR) {
            $PR = str_replace(['"', "'"], "", $PR);
            if ($isFirst) {
                $html .= sprintf(
                    '<a href="%s">%s#%s</a>',
                    $PR,
                    basename(dirname(dirname($PR))),
                    basename($PR)
                );
                $isFirst = false;
                continue;
            }

            $html .= sprintf(
                ', <a href="%s">%s#%s</a>',
                $PR,
                basename(dirname(dirname($PR))),
                basename($PR)
            );
        }

        return $html;
    }

    /**
     * @param array<string, array<string, mixed>> $groupedByLogin
     *
     * @return array<string, array<string, int>>
     */
    private function computeAndInsertTotals(array $groupedByLogin): array
    {
        $copy = $groupedByLogin;

        foreach ($groupedByLogin as $login => $dayStats) {
            $sum = 0;
            foreach ($dayStats as $dayStat) {
                if ($dayStat === 'no_data') {
                    continue;
                }

                $sum += $dayStat;
            }
            $copy[$login]['total'] = $sum;
        }

        return $copy;
    }

    private function buildDayStat(array $item): array
    {
        return [
            'day' => $item['day'],
            'PR' => $this->formatPRs($item['PR']),
            'total' => $item['total'],
            'total_peers' => $item['total_peers'],
            'total_community' => $item['total_community'],
        ];
    }

    private function insertIntoWeekStat(array $item, array $weekRangesTotals): array
    {
        $day = new DateTime($item['day']);
        foreach ($weekRangesTotals as $i => $weekRangesTotal) {
            if ($day >= $weekRangesTotal['begin'] && $day <= $weekRangesTotal['end']) {
                $weekRangesTotals[$i]['total'] += (int) $item['total'];
                $weekRangesTotals[$i]['total_peers'] += (int) $item['total_peers'];
                $weekRangesTotals[$i]['total_community'] += (int) $item['total_community'];
                break;
            }
        }

        return $weekRangesTotals;
    }


}
