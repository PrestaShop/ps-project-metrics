<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Tests\Helper;

use App\Helper\DayComputer;
use DateTime;
use PHPUnit\Framework\TestCase;

class DayComputerTest extends TestCase
{
    public function testGetPreviousWorkedDayFromWednesday(): void
    {
        $dateTime = new DateTime('2021-12-08 05:00:00');
        $expectedPreviousDay = new DateTime('2021-12-07 05:00:00');

        $this->assertEquals(
            $expectedPreviousDay->format('Y-m-d'),
            DayComputer::getPreviousWorkedDayFromDateTime($dateTime)->format('Y-m-d')
        );
    }

    public function testGetPreviousWorkedDayFromFriday(): void
    {
        $dateTime = new DateTime('2021-12-10 05:00:00');
        $expectedPreviousDay = new DateTime('2021-12-09 05:00:00');

        $this->assertEquals(
            $expectedPreviousDay->format('Y-m-d'),
            DayComputer::getPreviousWorkedDayFromDateTime($dateTime)->format('Y-m-d')
        );
    }

    public function testGetPreviousWorkedDayFromSaturday(): void
    {
        $dateTime = new DateTime('2021-12-11 05:00:00');
        $expectedPreviousDay = new DateTime('2021-12-10 05:00:00');

        $this->assertEquals(
            $expectedPreviousDay->format('Y-m-d'),
            DayComputer::getPreviousWorkedDayFromDateTime($dateTime)->format('Y-m-d')
        );
    }

    public function testGetPreviousWorkedDayFromSunday(): void
    {
        $dateTime = new DateTime('2021-12-12 05:00:00');
        $expectedPreviousDay = new DateTime('2021-12-10 05:00:00');

        $this->assertEquals(
            $expectedPreviousDay->format('Y-m-d'),
            DayComputer::getPreviousWorkedDayFromDateTime($dateTime)->format('Y-m-d')
        );
    }

    public function testGetPreviousWorkedDayFromMonday(): void
    {
        $dateTime = new DateTime('2021-12-13 05:00:00');
        $expectedPreviousDay = new DateTime('2021-12-10 05:00:00');

        $this->assertEquals(
            $expectedPreviousDay->format('Y-m-d'),
            DayComputer::getPreviousWorkedDayFromDateTime($dateTime)->format('Y-m-d')
        );
    }

    public function testIsItWeekend(): void
    {
        $dateTime1 = new DateTime('2021-12-21 05:00:00');
        $dateTime2 = new DateTime('2021-12-24 05:00:00');
        $dateTime3 = new DateTime('2021-12-25 05:00:00');
        $dateTime4 = new DateTime('2021-12-26 05:00:00');

        $this->assertFalse(DayComputer::isItWeekend($dateTime1));
        $this->assertFalse(DayComputer::isItWeekend($dateTime2));
        $this->assertTrue(DayComputer::isItWeekend($dateTime3));
        $this->assertTrue(DayComputer::isItWeekend($dateTime4));
    }

    public function testGetXDaysBefore(): void
    {
        $dateTime1 = new DateTime('2021-12-21 05:00:00');
        $dateTime2 = new DateTime('2021-12-26 10:00:00');

        $this->assertEquals(
            new DateTime('2021-12-19 05:00:00'),
            DayComputer::getXDayBefore(2, $dateTime1)
        );
        $this->assertEquals(
            new DateTime('2021-12-23 10:00:00'),
            DayComputer::getXDayBefore(3, $dateTime2)
        );

        $this->assertEquals(
            new DateTime('2021-12-14 05:00:00'),
            DayComputer::getXDayBefore(7, $dateTime1)
        );
        $this->assertEquals(
            new DateTime('2021-12-19 10:00:00'),
            DayComputer::getXDayBefore(7, $dateTime2)
        );
    }

    public function testBuildArrayOfDatesFromTo(): void
    {
        $dateTime1 = new DateTime('2021-12-21 05:00:00');
        $dateTime2 = new DateTime('2021-12-24 05:00:00');
        $dateTime3 = new DateTime('2021-12-26 05:00:00');


        $this->assertEquals(
            ['2021-12-21', '2021-12-22', '2021-12-23', '2021-12-24'],
            DayComputer::buildArrayOfDatesFromTo($dateTime1, $dateTime2)
        );

        $this->assertEquals(
            ['2021-12-21', '2021-12-22', '2021-12-23', '2021-12-24', '2021-12-25', '2021-12-26'],
            DayComputer::buildArrayOfDatesFromTo($dateTime1, $dateTime3)
        );
    }

    public function testGetSundayBefore(): void
    {
        $this->assertEquals(
            new DateTime('2022-02-20 05:00:00'),
            DayComputer::getSundayBefore(new DateTime('2022-02-20 05:00:00'))
        );

        $this->assertEquals(
            new DateTime('2022-02-20 05:00:00'),
            DayComputer::getSundayBefore(new DateTime('2022-02-21 05:00:00'))
        );

        $this->assertEquals(
            new DateTime('2022-02-20 05:00:00'),
            DayComputer::getSundayBefore(new DateTime('2022-02-24 05:00:00'))
        );

        $this->assertEquals(
            new DateTime('2022-02-20 05:00:00'),
            DayComputer::getSundayBefore(new DateTime('2022-02-26 05:00:00'))
        );

        $this->assertEquals(
            new DateTime('2022-02-06 05:00:00'),
            DayComputer::getSundayBefore(new DateTime('2022-02-06 05:00:00'))
        );

        $this->assertEquals(
            new DateTime('2022-02-13 02:00:00'),
            DayComputer::getSundayBefore(new DateTime('2022-02-19 02:00:00'))
        );
    }

    public function testGetOnePastWeekRange(): void
    {
        $this->assertEquals(
            [['2022-02-14', '2022-02-20']],
            DayComputer::getPastWeekRanges(1, new DateTime('2022-02-20 05:00:00'))
        );

        $this->assertEquals(
            [['2022-01-31', '2022-02-06']],
            DayComputer::getPastWeekRanges(1, new DateTime('2022-02-08 05:00:00'))
        );

        $this->assertEquals(
            [['2022-01-24', '2022-01-30']],
            DayComputer::getPastWeekRanges(1, new DateTime('2022-02-03 05:00:00'))
        );
    }

    public function testGetMultiplePastWeekRange(): void
    {
        $this->assertEquals(
            [['2022-01-10', '2022-01-16'], ['2022-01-17', '2022-01-23']],
            DayComputer::getPastWeekRanges(2, new DateTime('2022-01-24 05:00:00'))
        );

        $this->assertEquals(
            [['2021-12-20', '2021-12-26'], ['2021-12-27', '2022-01-02'], ['2022-01-03', '2022-01-09']],
            DayComputer::getPastWeekRanges(3, new DateTime('2022-01-10 05:00:00'))
        );
    }

    public function testFindWeekNumber(): void
    {
        $dateTime1 = new DateTime('2022-03-21 05:00:00');
        $dateTime2 = new DateTime('2022-03-22 05:00:00');
        $dateTime3 = new DateTime('2022-03-27 05:00:00');
        $dateTime4 = new DateTime('2022-03-20 05:00:00');

        $this->assertEquals(12, DayComputer::findWeekNumber($dateTime1));
        $this->assertEquals(12, DayComputer::findWeekNumber($dateTime2));
        $this->assertEquals(12, DayComputer::findWeekNumber($dateTime3));
        $this->assertEquals(11, DayComputer::findWeekNumber($dateTime4));
    }
}
