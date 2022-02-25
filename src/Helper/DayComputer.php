<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use DateInterval;
use DateTime;
use DatePeriod;

class DayComputer
{
    /**
     * @param DateTime $dateTime
     *
     * @return DateTime
     */
    public static function getPreviousWorkedDayFromDateTime(DateTime $dateTime): DateTime
    {
        $weekDay = $dateTime->format('w');
        $previousWorkedDay = clone $dateTime;

        switch ($weekDay) {
            case 0:
                $previousWorkedDay->sub(new DateInterval('P2D'));
                return $previousWorkedDay;
            case 1:
                $previousWorkedDay->sub(new DateInterval('P3D'));
                return $previousWorkedDay;
            default:
                $previousWorkedDay->sub(new DateInterval('P1D'));
                return $previousWorkedDay;
        }
    }


    /**
     * @param int $x
     * @param DateTime $dateTime
     *
     * @return DateTime
     */
    public static function getXDayBefore(int $x, DateTime $dateTime): DateTime
    {
        $previousWorkedDay = clone $dateTime;
        $previousWorkedDay->sub(new DateInterval('P' . $x . 'D'));

        return $previousWorkedDay;
    }

    /**
     * @param DateTime $dateTime
     *
     * @return bool
     */
    public static function isItWeekend(DateTime $dateTime): bool
    {
        $weekDay = $dateTime->format('w');

        return (in_array($weekDay, [0, 6]));
    }

    public static function buildArrayOfDatesFromTo(DateTime $from, DateTime $to): array
    {
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($from, $interval, $to);

        $result = [];

        foreach ($dateRange as $date) {
            $result[] = $date->format("Y-m-d");
        }

        $result[] = $to->format('Y-m-d');

        return $result;
    }

    public static function getSundayBefore(DateTime $day): DateTime
    {
        $weekDay = $day->format('w');

        if ($weekDay === '0') {
            return $day;
        }

        $result = clone $day;
        $result->sub(new DateInterval('P' . $weekDay . 'D'));

        return $result;
    }

    public static function getPastWeekRanges($numberOfWeeks, DateTime $day)
    {
        $sundayBefore = self::getSundayBefore($day);
        $mondayBefore = self::getXDayBefore(6, $sundayBefore);

        $result = [[$mondayBefore->format('Y-m-d'), $sundayBefore->format('Y-m-d')]];

        for($i = 1; $i < $numberOfWeeks; $i++) {
            $sundayBefore = self::getXDayBefore(7, $sundayBefore);
            $mondayBefore = self::getXDayBefore(7, $mondayBefore);
            $result[] = [$mondayBefore->format('Y-m-d'), $sundayBefore->format('Y-m-d')];
        }

        return array_reverse($result);
    }
}
