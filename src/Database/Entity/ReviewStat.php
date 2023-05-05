<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reviews
 *
 * @ORM\Table(name="reviews", uniqueConstraints={
 *     @ORM\UniqueConstraint(name="login_day_unique", columns={"login", "day"})
 * })
 * @ORM\Entity
 */
class ReviewStat
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
     * @var string|null
     *
     * @ORM\Column(name="login", type="string", length=255, nullable=true)
     */
    private $login;

    /**
     * @var string|null
     *
     * @ORM\Column(name="PR", type="text", length=65535, nullable=true)
     */
    private $pr;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="day", type="date", nullable=true)
     */
    private $day;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_peers", type="integer", nullable=true)
     */
    private $total_peers;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total_community", type="integer", nullable=true)
     */
    private $total_community;

    /**
     * @param string|null $login
     * @param string|null $pr
     * @param \DateTime|null $day
     * @param int|null $total_peers
     * @param int|null $total_community
     */
    public function __construct(?string $login, ?string $pr, ?\DateTime $day, ?int $total_peers, ?int $total_community)
    {
        $this->login = $login;
        $this->pr = $pr;
        $this->day = $day;
        $this->total_peers = $total_peers;
        $this->total_community = $total_community;
    }
}
