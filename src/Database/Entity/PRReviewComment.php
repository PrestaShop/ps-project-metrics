<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Review comment as delivered by webhook
 *
 * @ORM\Table(name="review_comment_webhook")
 * @ORM\Entity
 */
class PRReviewComment
{
    public const TYPE_PR_REVIEW_COMMENT = 'pr_review_comment';
    public const TYPE_PR_REVIEW_MAIN_MESSAGE = 'pr_review_comment_main_message';


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_number", type="string", length=255)
     */
    private $prNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_review_id", type="string", length=255)
     */
    private $prReviewId;

    /**
     * @var string
     *
     * @ORM\Column(name="comment_url", type="string", length=255)
     */
    private $commentUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_url", type="string", length=255)
     */
    private $prUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text")
     */
    private $commentBody;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="repo_name", type="string", length=255)
     */
    private $repoName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;

    /**
     * @param string $type
     * @param string $prNumber
     * @param string $prReviewId
     * @param string $commentUrl
     * @param string $prUrl
     * @param string $commentBody
     * @param string $author
     * @param string $repoName
     * @param \DateTime $createdAt
     */
    public function __construct(string $type, string $prNumber, string $prReviewId, string $commentUrl, string $prUrl, string $commentBody, string $author, string $repoName, \DateTime $createdAt)
    {
        $this->type = $type;
        $this->prNumber = $prNumber;
        $this->prReviewId = $prReviewId;
        $this->commentUrl = $commentUrl;
        $this->prUrl = $prUrl;
        $this->commentBody = $commentBody;
        $this->author = $author;
        $this->repoName = $repoName;
        $this->createdAt = $createdAt;
    }
}
