<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Helper\PRWaitingReviewStatusListingService;
use App\Helper\PRWaitingReviewStatusRecordService;
use App\Helper\ReviewStatsService;
use App\Helper\TeamHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PRWaitingReviewStatusController extends AbstractController
{
    /**
     * @var PRWaitingReviewStatusListingService
     */
    private PRWaitingReviewStatusListingService $listingService;

    /**
     * @param PRWaitingReviewStatusListingService $listingService
     */
    public function __construct(PRWaitingReviewStatusListingService $listingService)
    {
        $this->listingService = $listingService;
    }

    /**
     * @return Response
     */
    public function viewListing(): Response
    {
        $data = $this->listingService->getListingOfPRsWaitingForReview();

        return $this->render(
            'waiting_for_review.html.twig',
            [
                'PRs' => $data,
                'url' => PRWaitingReviewStatusRecordService::getUrl(),
            ]
        );
    }
}
