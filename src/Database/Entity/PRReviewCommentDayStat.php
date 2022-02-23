<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="review_comment_daily_stat")
 * @ORM\Entity
 */
class PRReviewCommentDayStat
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
     * @ORM\Column(name="login", type="string", length=255)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="details", type="text", length=65535)
     */
    private $details;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="day", type="date")
     */
    private $day;

    /**
     * @var int
     *
     * @ORM\Column(name="total", type="integer")
     */
    private $total;

    /**
     * @param string $login
     * @param string $details
     * @param \DateTime $day
     * @param int $total
     */
    public function __construct(string $login, string $details, \DateTime $day, int $total)
    {
        $this->login = $login;
        $this->details = $details;
        $this->day = $day;
        $this->total = $total;
    }


}
