<?php

namespace App\Controller;

use App\Entity\Experience;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ExperienceController extends AbstractController
{
    #[Route('/api/experience', name: 'app_get_experience', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $experiences = $em->getRepository(Experience::class)->findAll();

        return $this->json($experiences);
    }
}
