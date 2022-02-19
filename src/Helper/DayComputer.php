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
                $previousWorkedDay->sub(new \DateInterval('P2D'));
                return $previousWorkedDay;
            case 1:
                $previousWorkedDay->sub(new \DateInterval('P3D'));
                return $previousWorkedDay;
            default:
                $previousWorkedDay->sub(new \DateInterval('P1D'));
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
        $previousWorkedDay->sub(new \DateInterval('P' . $x . 'D'));

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
}
