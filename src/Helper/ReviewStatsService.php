<?php

declare(strict_types=1);

namespace App\Helper;

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
     * @param int $recordsNumber
     * @param int $skipRecordsNumber
     *
     * @return array<string, mixed>
     */
    public function getTeamStatsGroupedByLogin(int $recordsNumber, int $skipRecordsNumber): array
    {
        $sql = 'SELECT login, day, total FROM reviews ORDER BY day DESC LIMIT ' . $recordsNumber . ' OFFSET ' . $skipRecordsNumber;
        $result = $this->pdo->query($sql)->fetchAll();

        $days = [];
        $groupedByLogin = TeamHelper::getTeam(true);
        $total = 0;

        foreach ($result as $item) {
            $days[$item['day']] = $item['day'];
            $groupedByLogin = $this->addOrInsert(
                $groupedByLogin,
                $item['login'],
                $item['day'],
                (int)$item['total']
            );
            $total += (int)$item['total'];
        }

        foreach ($groupedByLogin as $login => $group) {
            $groupedByLogin[$login] = array_reverse($group);
        }
        $groupedByLogin = $this->computeAndInsertTotals($groupedByLogin);

        return [
            'days' => array_reverse($days),
            'lastSeven' => $groupedByLogin,
            'totalTeam' => $total,
        ];
    }

    /**
     * @param int $recordsNumber
     * @param int $skipRecordsNumber
     *
     * @return array<string, mixed>
     */
    public function getTeamStatsGroupedByDay(int $recordsNumber, int $skipRecordsNumber): array
    {
        $sql = 'SELECT login, day, total FROM reviews ORDER BY day DESC LIMIT ' . $recordsNumber . ' OFFSET ' . $skipRecordsNumber;
        $result = $this->pdo->query($sql)->fetchAll();

        $groupedByDay = [];

        foreach ($result as $item) {
            $groupedByDay = $this->addOrInsert(
                $groupedByDay,
                $item['day'],
                $item['login'],
                (int)$item['total']
            );
        }

        foreach ($groupedByDay as $day => $group) {
            $groupedByDay[$day] = TeamHelper::reorderByTeamOrder($group);
        }

        return [
            'teamMembers' => TeamHelper::getTeam(),
            'lastThirty' => $groupedByDay,
        ];
    }

    /**
     * @param string $login
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDeveloperStats(string $login): array
    {
        $sql = sprintf(
            "SELECT day, PR, total FROM reviews WHERE login = '%s' ORDER BY day DESC", $login);;
        $result = $this->pdo->query($sql)->fetchAll();

        $cleanResult = [];
        foreach ($result as $item) {
            $cleanResult[] = [
                'day' => $item['day'],
                'PR' => $this->formatPRs($item['PR']),
                'total' => $item['total'],
            ];
        }

        return $cleanResult;
    }

    /**
     * @param array<string, array<string, int>> $groupedByLogin
     * @param string $login
     * @param string $day
     * @param int $total
     *
     * @return array<string, array<string, int>>
     */
    private function addOrInsert(array $groupedByLogin, string $login, string $day, int $total): array
    {
        if (!array_key_exists($login, $groupedByLogin)) {
            $groupedByLogin[$login] = [];
        }
        if (!array_key_exists($day, $groupedByLogin[$login])) {
            $groupedByLogin[$login][$day] = [];
        }
        $groupedByLogin[$login][$day] = $total;

        return $groupedByLogin;
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
     * @param array<string, array<string, int>> $groupedByLogin
     *
     * @return array<string, array<string, int>>
     */
    private function computeAndInsertTotals(array $groupedByLogin): array
    {
        $copy = $groupedByLogin;

        foreach ($groupedByLogin as $login => $dayStats) {
            $sum = 0;
            foreach ($dayStats as $dayStat) {
                $sum += $dayStat;
            }
            $copy[$login]['total'] = $sum;
        }

        return $copy;
    }
}
