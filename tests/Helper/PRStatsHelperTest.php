<?php

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Database\Entity\PRWaitingStat;
use App\Helper\PRStatsHelper;
use DateTime;
use PHPUnit\Framework\TestCase;

class PRStatsHelperTest extends TestCase
{
    public function testGetTypesWithUrls()
    {
        $this->assertEquals(
            'https://api.github.com/search/issues?per_page=100&q=org%3APrestaShop+is%3Apr+is%3Aopen+label%3A"Waiting+for+QA"+archived%3Afalse',
            PRStatsHelper::getTypesWithUrls()['PR_WFQA']
        );
    }

    public function testGetTypesWithLabels()
    {
        $this->assertEquals(
            'Waiting for QA',
            PRStatsHelper::getTypesWithLabels()['PR_WFQA']
        );
    }

    public function testGetTypes()
    {
        $expected = [
            PRWaitingStat::PR_WAITING_FOR_REVIEW,
            PRWaitingStat::PR_WAITING_FOR_QA,
            PRWaitingStat::PR_WAITING_FOR_PM,
            PRWaitingStat::PR_WAITING_FOR_DEV,
            PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA,
            PRWaitingStat::PR_Open,
        ];

        $this->assertEquals($expected, PRStatsHelper::getTypes());
    }
}
