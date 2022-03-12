<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Helper\DayComputer;
use App\Helper\ReviewCommentStatsService;
use App\Helper\TeamHelper;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReviewCommentsStatsController extends AbstractController
{
    /**
     * @var ReviewCommentStatsService
     */
    private ReviewCommentStatsService $statisticsService;

    /**
     * @param ReviewCommentStatsService $statisticsService
     */
    public function __construct(ReviewCommentStatsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return Response
     */
    public function viewStats(): Response
    {
        $today = new DateTime();
        $yesterday = DayComputer::getXDayBefore(1, $today);
        $oneMonthBefore = DayComputer::getXDayBefore(30, $today);

        $lastThirtyDays = $this->statisticsService->getTeamStatsGroupedByDay($oneMonthBefore, $yesterday);

        $weekendDays = [];
        foreach ($lastThirtyDays as $day => $data) {
            if (DayComputer::isItWeekend(new DateTime($day))) {
                $weekendDays[] = $day;
            }
        }

        return $this->render(
            'review_comments_stats.html.twig',
            [
                'weekendDays' => $weekendDays,
                'teamMembers' => TeamHelper::getConfiguration(),
                'stats' => $lastThirtyDays,
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

        return $this->render('developer_review_comments.html.twig',
            ['stats' => $developerStats, 'login' => $login]
        );
    }
}
