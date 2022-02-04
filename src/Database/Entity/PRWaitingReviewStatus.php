<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pr_waiting_review_status")
 * @ORM\Entity
 */
class PRWaitingReviewStatus
{

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
     * @ORM\Column(name="pr_number", type="string", length=255)
     */
    private $prNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

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
     * @var int|null
     *
     * @ORM\Column(name="day_since_last_review", type="integer", nullable=true)
     */
    private $daySinceLastReview;

    /**
     * @var int|null
     *
     * @ORM\Column(name="day_since_last_commit", type="integer", nullable=true)
     */
    private $daySinceLastCommit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="date")
     */
    private $createdAt;

    /**
     * @param string $prNumber
     * @param string $name
     * @param string $url
     * @param string $author
     * @param string $repoName
     * @param int|null $daySinceLastReview
     * @param int|null $daySinceLastCommit
     * @param \DateTime $createdAt
     */
    public function __construct(string $prNumber, string $name, string $url, string $author, string $repoName, ?int $daySinceLastReview, ?int $daySinceLastCommit, \DateTime $createdAt)
    {
        $this->prNumber = $prNumber;
        $this->name = $name;
        $this->url = $url;
        $this->author = $author;
        $this->repoName = $repoName;
        $this->daySinceLastReview = $daySinceLastReview;
        $this->daySinceLastCommit = $daySinceLastCommit;
        $this->createdAt = $createdAt;
    }
}
