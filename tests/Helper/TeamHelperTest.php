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
            'matks',
            'jolelievre',
            'matthieu-rolland',
            'Progi1984',
            'atomiix',
            'NeOMakinG',
            'sowbiba',
            'kpodemski',
            'PululuK',
        ];

        $this->assertEquals($expected, TeamHelper::getTeam());
    }

    public function testGetTeamAsKeys(): void
    {
        $expected = [
            'matks' => [],              # Mathieu F.
            'jolelievre' => [],         # Jonathan L.
            'matthieu-rolland' => [],   # Matthieu R.
            'Progi1984' => [],          # Franck L.
            'atomiix' => [],            # Thomas B.
            'NeOMakinG' => [],          # Valentin S.
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
