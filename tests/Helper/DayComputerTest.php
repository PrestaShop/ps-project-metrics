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
}
