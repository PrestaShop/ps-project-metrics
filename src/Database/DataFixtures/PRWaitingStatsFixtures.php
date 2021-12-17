<?php

declare(strict_types=1);

namespace App\Database\DataFixtures;

use App\Database\Entity\PRWaitingStat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use DateTime;
use Doctrine\Persistence\ObjectManager;

class PRWaitingStatsFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_QA, new DateTime('2012-12-02'), 5));
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_DEV, new DateTime('2012-12-02'), 4));
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_DEV_AND_QA, new DateTime('2012-12-02'), 6));
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_REVIEW, new DateTime('2012-12-02'), 10));
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_PM, new DateTime('2012-12-02'), 2));
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_Open, new DateTime('2012-12-02'), 200));


        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_QA, new DateTime('2012-12-01'), 5));
        $manager->persist(new PRWaitingStat(PRWaitingStat::PR_WAITING_FOR_REVIEW, new DateTime('2012-12-01'), 5));

        $manager->flush();
    }
}
