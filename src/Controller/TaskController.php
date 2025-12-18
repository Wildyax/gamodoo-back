<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/task', name: 'app_task', methods: ['POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $task = new Task();
        $task_data = json_decode($request->getContent(), true);

        if (isset($task_data['label'])) $task->setLabel($task_data['label']);
        if (isset($task_data['description'])) $task->setDescription($task_data['description']);
        if (isset($task_data['level'])) $task->setLevel($task_data['level']);
        if (isset($task_data['tags'])) $task->setTags($task_data['tags']);
        if (isset($task_data['checked'])) $task->setChecked($task_data['checked']);

        $entityManager->persist($task);
        $entityManager->flush();

        return $this->json([
            'data' => $task_data,
            'success' => (bool)$task,
        ]);
    }

    #[Route('/task', name: 'app_task_all', methods: ['GET'])]
    public function all(TaskRepository $taskRepository): JsonResponse
    {
        $tasks = $taskRepository->findAll();

        return $this->json([
            'data' => $tasks,
            'success' => true,
        ]);
    }

    #[Route('/task/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(?Task $task)
    {
        return $this->json([
            'data' => $task ?? [],
            'success' => (bool)$task,
        ]);
    }

    #[Route('/task/{id}', name: 'app_task_edit', methods: ['PUT'])]
    public function update(EntityManagerInterface $entityManager, ?Task $task, Request $request, TaskRepository $taskRepository): JsonResponse
    {
        $task_data = json_decode($request->getContent(), true);

        if (isset($task_data['label'])) $task->setLabel($task_data['label']);
        if (isset($task_data['description'])) $task->setDescription($task_data['description']);
        if (isset($task_data['level'])) $task->setLevel($task_data['level']);
        if (isset($task_data['tags'])) $task->setTags($task_data['tags']);
        if (isset($task_data['checked'])) $task->setChecked($task_data['checked']);

        $entityManager->flush();

        return $this->json([
            'data' => $task,
            'success' => (bool)$task
        ]);
    }

    #[Route('/task/{id}', name: 'app_task_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, ?Task $task, Request $request, TaskRepository $taskRepository): JsonResponse
    {
        if($task)
        {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->json([
            'success' => (bool)$task,
        ]);
    }
}
