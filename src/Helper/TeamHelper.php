<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

class TeamHelper
{
    /**
     * @param bool $asKeys
     *
     * @return string[]|array<string, array<int, int>>
     */
    public static function getTeam(bool $asKeys = false): array
    {
        $team = self::getConfiguration();

        if ($asKeys) {
            return array_map((function () {
                return [];
            }), $team);
        }

        return array_keys($team);
    }

    /**
     * @param array<string, mixed> $groupedByLogin
     *
     * @return array<string, mixed>
     */
    public static function reorderByTeamOrder(array $groupedByLogin): array
    {
        $reordered = array_fill_keys(self::getTeam(), 0);

        foreach ($groupedByLogin as $login => $group) {
            $reordered[$login] = $group;
        }

        return $reordered;
    }

    /**
     * @return array
     */
    public static function getConfiguration(): array
    {
        $team = [
            'jolelievre' => ['full-time' => true],         # Jonathan L.
            'matthieu-rolland' => ['full-time' => true],   # Matthieu R.
            'atomiix' => ['full-time' => true],            # Thomas B.
            'NeOMakinG' => ['full-time' => true],          # Valentin S.
            'matks' => ['full-time' => false],             # Mathieu F.
            'sowbiba' => ['full-time' => false],           # Ibrahima S.
            'kpodemski' => ['full-time' => false],         # Krystian P.
            'PululuK' => ['full-time' => false],           # Pululu K.
        ];

        return $team;
    }
}
