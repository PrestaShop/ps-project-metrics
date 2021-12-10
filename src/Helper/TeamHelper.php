<?php

declare(strict_types=1);

namespace App\Helper;

class TeamHelper
{
    /**
     * @param bool $asKeys
     *
     * @return string[]|array[]
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
        ];

        if ($asKeys) {
            return $team;
        }

        return array_keys($team);
    }
}
