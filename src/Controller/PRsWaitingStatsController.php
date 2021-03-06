<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Helper\PRsWaitingStatsService;
use App\Helper\TeamHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PRsWaitingStatsController extends AbstractController
{
    /**
     * @var PRsWaitingStatsService
     */
    private PRsWaitingStatsService $statisticsService;

    /**
     * @param PRsWaitingStatsService $statisticsService
     */
    public function __construct(PRsWaitingStatsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    /**
     * @return Response
     */
    public function viewStats(): Response
    {
        $stats = $this->statisticsService->getTeamStatsGroupedByDay(180);

        return $this->render(
            'pr_waiting_stats.html.twig',
            $stats
        );
    }
}
