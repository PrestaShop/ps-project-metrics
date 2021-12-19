<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="pr_waiting")
 * @ORM\Entity
 */
class PRWaitingStat
{
    const PR_WAITING_FOR_REVIEW = 'PR_WFReview';
    const PR_WAITING_FOR_QA = 'PR_WFQA';
    const PR_WAITING_FOR_PM = 'PR_WFPM';
    const PR_WAITING_FOR_DEV = 'PR_WFDev';
    const PR_WAITING_FOR_DEV_AND_QA = 'PR_WFDevAndQA';
    const PR_Open = 'PR_AllOpen';

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
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="day", type="date", nullable=true)
     */
    private $day;

    /**
     * @var int|null
     *
     * @ORM\Column(name="total", type="integer", nullable=true)
     */
    private $total;

    /**
     * @param string|null $name
     * @param \DateTime|null $day
     * @param int|null $total
     */
    public function __construct(?string $name, ?\DateTime $day, ?int $total)
    {
        $this->name = $name;
        $this->day = $day;
        $this->total = $total;
    }
}
