<?php

namespace App\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reviews
 *
 * @ORM\Table(name="reviews")
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
     * @ORM\Column(name="total", type="integer", nullable=true)
     */
    private $total;
}
