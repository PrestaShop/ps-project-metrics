<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Helper\DayComputer;
use App\Helper\ReviewStatsService;
use App\Helper\TeamHelper;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReviewStatsHomeController extends AbstractController
{
    /**
     * @var ReviewStatsService
     */
    private ReviewStatsService $statisticsService;

    /**
     * @param ReviewStatsService $statisticsService
     */
    public function __construct(ReviewStatsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return Response
     */
    public function home(): Response
    {
        $today = new DateTime();
        $yesterday = DayComputer::getXDayBefore(1, $today);
        $eightDaysBefore = DayComputer::getXDayBefore(8, $today);
        $nineDaysBefore = DayComputer::getXDayBefore(9, $today);
        $oneMonthBefore = DayComputer::getXDayBefore(30, $today);

        $lastSevenAndDays = $this->statisticsService->getTeamStatsGroupedByLogin($eightDaysBefore, $yesterday);
        $lastThirtyDays = $this->statisticsService->getTeamStatsGroupedByDay($oneMonthBefore, $nineDaysBefore);

        $weekendDays = [];
        foreach ($lastSevenAndDays['days'] as $day) {
            if (DayComputer::isItWeekend(new DateTime($day))) {
                $weekendDays[] = $day;
            }
        }
        foreach ($lastThirtyDays as $day => $data) {
            if (DayComputer::isItWeekend(new DateTime($day))) {
                $weekendDays[] = $day;
            }
        }

        return $this->render(
            'review_stats.html.twig',
            [
                'weekendDays' => $weekendDays,
                'teamMembers' => TeamHelper::getTeam(),
                'lastSeven' => $lastSevenAndDays,
                'lastThirty' => $lastThirtyDays,
            ]
        );
    }

    /**
     * @param string $login
     *
     * @return Response
     */
    public function viewDeveloper(string $login): Response
    {
        if (!in_array($login, TeamHelper::getTeam())) {
            throw $this->createNotFoundException('No developer');
        }

        $developerStats = $this->statisticsService->getDeveloperStats($login);

        return $this->render('developer_stats.html.twig',
            ['stats' => $developerStats, 'login' => $login]
        );
    }
}
