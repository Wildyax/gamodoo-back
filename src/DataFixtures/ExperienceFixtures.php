<?php

namespace App\DataFixtures;

use App\Entity\Experience;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ExperienceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        if (count($manager->getRepository(Experience::class)->findAll()) > 0) {
            return;
        }

        $base_experience = 100;
        for ($i = 1; $i < 51; $i ++) {
            $experience = new Experience();
            $experience->setLevel($i);
            $experience->setExp(round($base_experience * ($i ** 1.8)));

            $manager->persist($experience);
        }

        $manager->flush();
    }
}