<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Database\Entity\PRWaitingStat;
use App\Helper\PRStatsHelper;
use DateTime;
use PHPUnit\Framework\TestCase;

class PRStatsHelperTest extends TestCase
{
    public function testGetTypesWithUrls(): void
    {
        $this->assertEquals(
            'https://api.github.com/search/issues?per_page=100&q=org%3APrestaShop+is%3Apr+is%3Aopen+label%3A"Waiting+for+QA"+archived%3Afalse',
            PRStatsHelper::getTypesWithUrls()['PR_WFQA']
        );
    }

    public function testGetTypesWithLabels(): void
    {
        $this->assertEquals(
            'Waiting for QA',
            PRStatsHelper::getTypesWithLabels()['PR_WFQA']
        );
    }

    public function testGetTypes(): void
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

    public function testReorderByPRTypeOrder(): void
    {
        $input = [
            PRWaitingStat::PR_WAITING_FOR_QA => 10,
            PRWaitingStat::PR_WAITING_FOR_REVIEW => 4,
            PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA => 28,
            PRWaitingStat::PR_WAITING_FOR_PM => 0,
            PRWaitingStat::PR_Open => 82,
            PRWaitingStat::PR_WAITING_FOR_DEV => 17,
        ];
        $expected = [
            PRWaitingStat::PR_WAITING_FOR_REVIEW => 4,
            PRWaitingStat::PR_WAITING_FOR_QA => 10,
            PRWaitingStat::PR_WAITING_FOR_PM => 0,
            PRWaitingStat::PR_WAITING_FOR_DEV => 17,
            PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA => 28,
            PRWaitingStat::PR_Open => 82,
        ];

        $this->assertEquals($expected, PRStatsHelper::reorderByPRTypeOrder($input));
    }
}
