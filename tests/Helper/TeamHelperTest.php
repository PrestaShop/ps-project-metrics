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
            'jolelievre',
            'matthieu-rolland',
            'atomiix',
            'NeOMakinG',
            'matks',
            'Progi1984',
            'sowbiba',
            'kpodemski',
            'PululuK',
        ];

        $this->assertEquals($expected, TeamHelper::getTeam());
    }

    public function testGetTeamAsKeys(): void
    {
        $expected = [
            'jolelievre' => [],         # Jonathan L.
            'matthieu-rolland' => [],   # Matthieu R.
            'atomiix' => [],            # Thomas B.
            'NeOMakinG' => [],          # Valentin S.
            'matks' => [],              # Mathieu F.
            'Progi1984' => [],          # Franck L.
            'sowbiba' => [],            # Ibrahima S.
            'kpodemski' => [],          # Krystian P.
            'PululuK' => [],
        ];


        $this->assertEquals($expected, TeamHelper::getTeam(true));
    }

    public function testReorderByTeamOrder(): void
    {
        $input = [
            'matks' => 1,
            'sowbiba' => 62,
            'jolelievre' => 28,
            'Progi1984' => 91,
            'PululuK' => 27,
            'atomiix' => 82,
            'NeOMakinG' => 2,
            'matthieu-rolland' => 29,
            'kpodemski' => 72,
        ];

        $expected = [
            'matks' => 1,
            'jolelievre' => 28,
            'matthieu-rolland' => 29,
            'Progi1984' => 91,
            'atomiix' => 82,
            'NeOMakinG' => 2,
            'sowbiba' => 62,
            'kpodemski' => 72,
            'PululuK' => 27,
        ];

        $this->assertEquals($expected, TeamHelper::reorderByTeamOrder($input));
    }
}
