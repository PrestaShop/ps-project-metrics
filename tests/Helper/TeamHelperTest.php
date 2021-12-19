<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\TeamHelper;
use PHPUnit\Framework\TestCase;

class TeamHelperTest extends TestCase
{
    public function testGetTeam(): void
    {
        $expected = [
            'PierreRambaud',
            'matks',
            'jolelievre',
            'matthieu-rolland',
            'Progi1984',
            'atomiix',
            'NeOMakinG',
            'sowbiba',
        ];

        $this->assertEquals($expected, TeamHelper::getTeam());
    }

    public function testGetTeamAsKeys(): void
    {
        $expected = [
            'PierreRambaud' => [],      # Pierre R.
            'matks' => [],              # Mathieu F.
            'jolelievre' => [],         # Jonathan L.
            'matthieu-rolland' => [],   # Matthieu R.
            'Progi1984' => [],          # Franck L.
            'atomiix' => [],            # Thomas B.
            'NeOMakinG' => [],          # Valentin S.
            'sowbiba' => [],            # Ibrahima S.
        ];


        $this->assertEquals($expected, TeamHelper::getTeam(true));
    }

    public function testReorderByTeamOrder(): void
    {
        $input = [
            'matks' => 1,              # Mathieu F.
            'sowbiba' => 62,            # Ibrahima S.
            'jolelievre' => 28,         # Jonathan L.
            'Progi1984' => 91,          # Franck L.
            'PierreRambaud' => 19,      # Pierre R.
            'atomiix' => 82,            # Thomas B.
            'NeOMakinG' => 2,          # Valentin S.
            'matthieu-rolland' => 29,   # Matthieu R.
        ];

        $expected = [
            'PierreRambaud' => 19,      # Pierre R.
            'matks' => 1,              # Mathieu F.
            'jolelievre' => 28,         # Jonathan L.
            'matthieu-rolland' => 29,   # Matthieu R.
            'Progi1984' => 91,          # Franck L.
            'atomiix' => 82,            # Thomas B.
            'NeOMakinG' => 2,          # Valentin S.
            'sowbiba' => 62,            # Ibrahima S.
        ];

        $this->assertEquals($expected, TeamHelper::reorderByTeamOrder($input));
    }
}
