<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\ReviewStatsService;
use App\Helper\TeamHelper;
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
        $teamSize = 8;
        $sevenDays = 7;
        $twentyThreeDays = 23;

        $lastSevenAndDays = $this->statisticsService->getTeamStatsGroupedByLogin($teamSize * $sevenDays, 0);

        $lastThirtyDays = $this->statisticsService->getTeamStatsGroupedByDay($teamSize * $twentyThreeDays, $teamSize * $sevenDays);

        return $this->render(
            'main.html.twig',
            [
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

        return $this->render('developer.html.twig',
            ['stats' => $developerStats, 'login' => $login]
        );
    }
}
