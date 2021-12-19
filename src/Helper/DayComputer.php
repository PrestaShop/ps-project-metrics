<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Helper;

use DateTime;

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
}
