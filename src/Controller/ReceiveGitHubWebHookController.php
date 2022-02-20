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

class ReceiveGitHubWebHookController extends AbstractController
{

    /**
     * @return Response
     */
    public function receive(): Response
    {
        return new Response();
    }
}
