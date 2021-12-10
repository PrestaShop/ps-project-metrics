<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\StatsService;
use App\Helper\TeamHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class MainController extends AbstractController
{
    /**
     * @var StatsService
     */
    private $statisticsService;

    /**
     * @param StatsService $statisticsService
     */
    public function __construct(StatsService $statisticsService)
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

        $lastThirtyDays = $this->statisticsService->getTeamStatsGroupedByDay($teamSize * $twentyThreeDays, 0);//$teamSize * $sevenDays);

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
