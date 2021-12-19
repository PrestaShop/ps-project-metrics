<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\ReviewStatsService;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use PDO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReviewStatsServiceTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    /** @var string */
    protected string $sqliteFilepath;

    public function setUp(): void
    {
        parent::setUp();

        $container = static::getContainer();
        $this->databaseTool = $container->get(DatabaseToolCollection::class)->get();
        $this->sqliteFilepath = $container->getParameter('kernel.cache_dir') . '/test.db';
    }

    public function testGetDeveloperStatsFromMatks(): void
    {
        $this->databaseTool->loadFixtures(['App\Database\DataFixtures\ReviewStatsFixtures']);

        $statsService = new ReviewStatsService($this->getPDO());

        $stats = $statsService->getDeveloperStats('matks');

        $this->assertEquals($stats[0]['day'], '2021-12-05');
        $this->assertEquals($stats[0]['total'], 2);
        $this->assertEquals($stats[4]['day'], '2021-12-01');
        $this->assertEquals($stats[4]['total'], 1);
    }

    public function testGetDeveloperStatsFromPierreRambaud(): void
    {
        $this->databaseTool->loadFixtures(['App\Database\DataFixtures\ReviewStatsFixtures']);

        $statsService = new ReviewStatsService($this->getPDO());

        $stats = $statsService->getDeveloperStats('PierreRambaud');

        $this->assertEquals($stats[0]['day'], '2021-12-02');
        $this->assertEquals($stats[0]['total'], 20);
        $this->assertEquals($stats[1]['day'], '2021-12-01');
        $this->assertEquals($stats[1]['total'], 10);
    }

    public function testGetTeamStatsGroupedByLogin(): void
    {
        $this->databaseTool->loadFixtures(['App\Database\DataFixtures\ReviewStatsFixtures']);

        $statsService = new ReviewStatsService($this->getPDO());

        $stats = $statsService->getTeamStatsGroupedByLogin(10, 0);

        $expected = ["days" => [
            "2021-12-01" => "2021-12-01",
            "2021-12-02" => "2021-12-02",
            "2021-12-03" => "2021-12-03",
            "2021-12-04" => "2021-12-04",
            "2021-12-05" => "2021-12-05",
        ],
            "lastSeven" => [
                "PierreRambaud" => [
                    "2021-12-01" => 10,
                    "2021-12-02" => 20,
                    "total" => 30,
                ],
                "matks" => [
                    "2021-12-01" => 1,
                    "2021-12-02" => 2,
                    "2021-12-03" => 3,
                    "2021-12-04" => 4,
                    "2021-12-05" => 2,
                    "total" => 12,
                ],
                "jolelievre" => [
                    "total" => 0,
                ],
                "matthieu-rolland" => [
                    "total" => 0,
                ],
                "Progi1984" => [
                    "total" => 0,
                ],
                "atomiix" => [
                    "2021-12-01" => 3,
                    "2021-12-03" => 4,
                    "2021-12-05" => 2,
                    "total" => 9,
                ],
                "NeOMakinG" => [
                    "total" => 0,
                ],
                "sowbiba" => [
                    "total" => 0,
                ]
            ],
            "totalTeam" => 51
        ];

        $this->assertEquals($expected, $stats);
    }

    public function testGetTeamStatsGroupedByDay(): void
    {
        $this->databaseTool->loadFixtures(['App\Database\DataFixtures\ReviewStatsFixtures']);

        $statsService = new ReviewStatsService($this->getPDO());

        $stats = $statsService->getTeamStatsGroupedByDay(10, 0);

        $expected = ["teamMembers" => [
            0 => "PierreRambaud",
            1 => "matks",
            2 => "jolelievre",
            3 => "matthieu-rolland",
            4 => "Progi1984",
            5 => "atomiix",
            6 => "NeOMakinG",
            7 => "sowbiba",
        ],
            "lastThirty" => [
                "2021-12-05" => [
                    "PierreRambaud" => 0,
                    "matks" => 2,
                    "jolelievre" => 0,
                    "matthieu-rolland" => 0,
                    "Progi1984" => 0,
                    "atomiix" => 2,
                    "NeOMakinG" => 0,
                    "sowbiba" => 0,
                ],
                "2021-12-04" => [
                    "PierreRambaud" => 0,
                    "matks" => 4,
                    "jolelievre" => 0,
                    "matthieu-rolland" => 0,
                    "Progi1984" => 0,
                    "atomiix" => 0,
                    "NeOMakinG" => 0,
                    "sowbiba" => 0,
                ],
                "2021-12-03" => [
                    "PierreRambaud" => 0,
                    "matks" => 3,
                    "jolelievre" => 0,
                    "matthieu-rolland" => 0,
                    "Progi1984" => 0,
                    "atomiix" => 4,
                    "NeOMakinG" => 0,
                    "sowbiba" => 0,
                ],
                "2021-12-02" => [
                    "PierreRambaud" => 20,
                    "matks" => 2,
                    "jolelievre" => 0,
                    "matthieu-rolland" => 0,
                    "Progi1984" => 0,
                    "atomiix" => 0,
                    "NeOMakinG" => 0,
                    "sowbiba" => 0,
                ],
                "2021-12-01" => [
                    "PierreRambaud" => 10,
                    "matks" => 1,
                    "jolelievre" => 0,
                    "matthieu-rolland" => 0,
                    "Progi1984" => 0,
                    "atomiix" => 3,
                    "NeOMakinG" => 0,
                    "sowbiba" => 0,
                ]
            ]
        ];

        $this->assertEquals($expected, $stats);
    }

    /**
     * @return PDO
     */
    protected function getPDO(): PDO
    {
        $db = new PDO("sqlite:" . $this->sqliteFilepath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $db;
    }
}
