<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\ReviewStatsService;
use DateTime;
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

        $stats = $statsService->getTeamStatsGroupedByLogin(new DateTime('2021-12-01'), new DateTime('2021-12-05'));

        $emptyData = [
            "2021-12-01" => 'no_data',
            "2021-12-02" => 'no_data',
            "2021-12-03" => 'no_data',
            "2021-12-04" => 'no_data',
            "2021-12-05" => 'no_data',
            "total" => 0,
        ];

        $expected = ["days" => [
            "2021-12-01",
            "2021-12-02",
            "2021-12-03",
            "2021-12-04",
            "2021-12-05",
        ],
            "lastSeven" => [
                "PierreRambaud" => [
                    "2021-12-01" => 10,
                    "2021-12-02" => 20,
                    "2021-12-03" => 'no_data',
                    "2021-12-04" => 'no_data',
                    "2021-12-05" => 'no_data',
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
                "jolelievre" => $emptyData,
                "matthieu-rolland" => $emptyData,
                "Progi1984" => $emptyData,
                "atomiix" => [
                    "2021-12-01" => 3,
                    "2021-12-02" => 'no_data',
                    "2021-12-03" => 4,
                    "2021-12-04" => 'no_data',
                    "2021-12-05" => 2,
                    "total" => 9,
                ],
                "NeOMakinG" => $emptyData,
                "sowbiba" => $emptyData,
                'kpodemski' => $emptyData,
            ],
            "totalTeam" => 51
        ];

        $this->assertEquals($expected, $stats);
    }

    public function testGetTeamStatsGroupedByDay(): void
    {
        $this->databaseTool->loadFixtures(['App\Database\DataFixtures\ReviewStatsFixtures']);

        $statsService = new ReviewStatsService($this->getPDO());

        $stats = $statsService->getTeamStatsGroupedByDay(new DateTime('2021-12-01'), new DateTime('2021-12-05'));

        $expected = [
            "2021-12-05" => [
                "PierreRambaud" => 'no_data',
                "matks" => 2,
                "jolelievre" => 'no_data',
                "matthieu-rolland" => 'no_data',
                "Progi1984" => 'no_data',
                "atomiix" => 2,
                "NeOMakinG" => 'no_data',
                "sowbiba" => 'no_data',
                "kpodemski" => 'no_data',
            ],
            "2021-12-04" => [
                "PierreRambaud" => 'no_data',
                "matks" => 4,
                "jolelievre" => 'no_data',
                "matthieu-rolland" => 'no_data',
                "Progi1984" => 'no_data',
                "atomiix" => 'no_data',
                "NeOMakinG" => 'no_data',
                "sowbiba" => 'no_data',
                "kpodemski" => 'no_data',
            ],
            "2021-12-03" => [
                "PierreRambaud" => 'no_data',
                "matks" => 3,
                "jolelievre" => 'no_data',
                "matthieu-rolland" => 'no_data',
                "Progi1984" => 'no_data',
                "atomiix" => 4,
                "NeOMakinG" => 'no_data',
                "sowbiba" => 'no_data',
                "kpodemski" => 'no_data',
            ],
            "2021-12-02" => [
                "PierreRambaud" => 20,
                "matks" => 2,
                "jolelievre" => 'no_data',
                "matthieu-rolland" => 'no_data',
                "Progi1984" => 'no_data',
                "atomiix" => 'no_data',
                "NeOMakinG" => 'no_data',
                "sowbiba" => 'no_data',
                "kpodemski" => 'no_data',
            ],
            "2021-12-01" => [
                "PierreRambaud" => 10,
                "matks" => 1,
                "jolelievre" => 'no_data',
                "matthieu-rolland" => 'no_data',
                "Progi1984" => 'no_data',
                "atomiix" => 3,
                "NeOMakinG" => 'no_data',
                "sowbiba" => 'no_data',
                "kpodemski" => 'no_data',
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
