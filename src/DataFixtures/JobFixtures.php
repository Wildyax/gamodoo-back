<?php

namespace App\DataFixtures;

use App\Entity\Job;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class JobFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        if (count($manager->getRepository(Job::class)->findAll()) > 0) {
            return;
        }

        $jobs = [
            [
                'name' => 'Assassin',
                'description' => trim('Spécialiste des attaques rapides et furtives, l’Assassin excelle dans l’élimination de ses ennemis avant même qu’ils ne réagissent. Il privilégie la discrétion, la mobilité et les coups critiques, au prix d’une faible résistance.'),
            ],
            [
                'name' => 'Magicien',
                'description' => trim('Maître des arcanes, le Magicien canalise des pouvoirs mystiques pour infliger de lourds dégâts à distance ou altérer le champ de bataille. Puissant mais fragile, il doit gérer ses ressources avec intelligence pour survivre.'),
            ],
            [
                'name' => 'Archer',
                'description' => trim('Tireur d’élite capable de frapper à longue distance, l’Archer mise sur la précision et la vitesse. Polyvalent et mobile, il contrôle ses ennemis tout en évitant le combat rapproché.'),
            ],
            [
                'name' => 'Épéiste',
                'description' => trim('Combattant équilibré et discipliné, l’Épéiste maîtrise l’art du combat au corps à corps. Résistant et polyvalent, il peut s’adapter à de nombreuses situations et encaisser les coups tout en infligeant des dégâts constants.'),
            ],
        ];

        foreach ($jobs as $job_values) {
            $job = new Job();
            $job->setName($job_values['name']);
            $job->setDescription($job_values['description']);

            $manager->persist($job);
        }

        $manager->flush();
    }
}
