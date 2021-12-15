<?php

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
