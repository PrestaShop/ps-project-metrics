<?php

declare(strict_types=1);

namespace App\Database\DataFixtures;

use App\Database\Entity\ReviewStat;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReviewStatsFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $manager->persist(new ReviewStat('matks', '"ABC"', new DateTime('2021-12-01'), 1));
        $manager->persist(new ReviewStat('matks', '"ABC","ABD"', new DateTime('2021-12-02'), 2));
        $manager->persist(new ReviewStat('matks', '"ABD","ABA","ABE"', new DateTime('2021-12-03'), 3));
        $manager->persist(new ReviewStat('matks', '"ABD","ABA","ABC","ABI"', new DateTime('2021-12-04'), 4));
        $manager->persist(new ReviewStat('matks', '"ABC","ABD"', new DateTime('2021-12-05'), 2));

        $manager->persist(new ReviewStat('PierreRambaud', '"ABC"', new DateTime('2021-12-01'), 1));
        $manager->persist(new ReviewStat('PierreRambaud', '"ABC","ABD"', new DateTime('2021-12-02'), 2));

        $manager->persist(new ReviewStat('atomiix', '"ABD","ABA","ABE"', new DateTime('2021-12-01'), 3));
        $manager->persist(new ReviewStat('atomiix', '"ABD","ABA","ABC","ABI"', new DateTime('2021-12-03'), 4));
        $manager->persist(new ReviewStat('atomiix', '"ABC","ABD"', new DateTime('2021-12-05'), 2));

        $manager->flush();
    }
}
