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
        $team = [
            'PierreRambaud' => [],      # Pierre R.
            'matks' => [],              # Mathieu F.
            'jolelievre' => [],         # Jonathan L.
            'matthieu-rolland' => [],   # Matthieu R.
            'Progi1984' => [],          # Franck L.
            'atomiix' => [],            # Thomas B.
            'NeOMakinG' => [],          # Valentin S.
            'sowbiba' => [],            # Ibrahima S.
            'kpodemski' => [],          # Krystian P.
        ];

        if ($asKeys) {
            return $team;
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
}
