<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Database\Entity\PRWaitingStat;
use App\Helper\PRsWaitingStatsService;
use App\Helper\ReviewStatsService;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use PDO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PRsWaitingStatsServiceTest extends KernelTestCase
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
        $this->databaseTool->loadFixtures(['App\Database\DataFixtures\PRWaitingStatsFixtures']);

        $statsService = new PRsWaitingStatsService($this->getPDO());

        $data = $statsService->getTeamStatsGroupedByDay(6);
        $stats = $data['stats'];
        $expected = [
            PRWaitingStat::PR_WAITING_FOR_REVIEW => 10,
            PRWaitingStat::PR_WAITING_FOR_QA => 5,
            PRWaitingStat::PR_WAITING_FOR_PM => 2,
            PRWaitingStat::PR_WAITING_FOR_DEV => 4,
            PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA => 6,
            PRWaitingStat::PR_Open => 200,
        ];

        $this->assertEquals($expected, $stats['2012-12-02']);
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
