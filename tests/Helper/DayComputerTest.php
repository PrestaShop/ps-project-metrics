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
}
