<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\TeamHelper;
use PHPUnit\Framework\TestCase;

class TeamHelperTest extends TestCase
{
    public function testGetTeam()
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

    public function testGetTeamAsKeys()
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
}
