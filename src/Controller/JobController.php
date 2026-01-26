<?php

namespace App\Controller;

use App\Entity\Job;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class JobController extends AbstractController
{
    #[Route('/api/jobs', name: 'api_job_get', methods: ['GET'])]
    public function showJobs(
        EntityManagerInterface $em,
    ): JsonResponse
    {
        $jobs = $em->getRepository(Job::class)->findAll();

        return $this->json($jobs);
    }
}
