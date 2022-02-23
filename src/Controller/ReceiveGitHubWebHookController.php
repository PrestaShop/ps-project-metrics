<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Database\Entity\PRReviewComment;
use App\Helper\TeamHelper;
use DateTime;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ReceiveGitHubWebHookController extends AbstractController
{
    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Response
     */
    public function receive(Request $request): Response
    {
        $json = json_decode($request->getContent(), true);

        if (!$json) {
            return new Response();
        }

        $isAPRReviewComment = isset($json['comment'])
            && isset($json['comment']['user'])
            && isset($json['repository']);
        $isAMainPRReviewComment = isset($json['review']);

        if (!$isAPRReviewComment && !$isAMainPRReviewComment) {
            return new Response();
        }

        if ($isAPRReviewComment) {
            $author = $json['comment']['user']['login'];
        } else {
            $author = $json['review']['user']['login'];
        }

        if (!in_array($author, TeamHelper::getTeam())) {
            return new Response();
        }

        if ($isAPRReviewComment) {
            $comment = new PRReviewComment(
                PRReviewComment::TYPE_PR_REVIEW_COMMENT,
                basename($json['comment']['pull_request_url']),
                (string)$json['comment']['pull_request_review_id'],
                $json['comment']['html_url'],
                $json['pull_request']['html_url'],
                $json['comment']['body'],
                $author,
                $json['repository']['full_name'],
                new DateTime($json['comment']['created_at'])
            );
        } else {
            $comment = new PRReviewComment(
                PRReviewComment::TYPE_PR_REVIEW_MAIN_MESSAGE,
                (string)$json['pull_request']['number'],
                (string)$json['review']['id'],
                $json['review']['html_url'],
                $json['pull_request']['html_url'],
                $json['review']['body'],
                $author,
                $json['repository']['full_name'],
                new DateTime($json['review']['submitted_at'])
            );
        }

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        return new Response();
    }
}
