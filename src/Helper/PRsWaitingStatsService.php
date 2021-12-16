<?php

declare(strict_types=1);

namespace App\Helper;

use PDO;

class PRsWaitingStatsService
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
     *
     * @return array
     */
    public function getTeamStatsGroupedByDay(int $recordsNumber): array
    {
        $sql = 'SELECT name, day, total FROM pr_waiting ORDER BY day DESC LIMIT ' . $recordsNumber;
        $result = $this->pdo->query($sql)->fetchAll();

        $groupedByDay = [];

        foreach ($result as $item) {
            $groupedByDay = $this->addOrInsert(
                $groupedByDay,
                $item['day'],
                $item['name'],
                (int) $item['total']
            );
        }

        foreach ($groupedByDay as $day => $group) {
            $groupedByDay[$day] = PRStatsHelper::reorderByPRTypeOrder($group);
        }

        return [
            'prTypes' => PRStatsHelper::getTypesWithLabels(),
            'prUrls' => PRStatsHelper::getTypesWithUrls(),
            'stats' => $groupedByDay,
        ];
    }

    /**
     * @param array $groupedByLogin
     * @param string $login
     * @param string $day
     * @param int $total
     *
     * @return array
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
}
