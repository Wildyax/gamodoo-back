<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\ExperienceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class TaskController extends AbstractController
{
    #[Route('/api/task', name: 'app_task', methods: ['POST'])]
    public function create(EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();

        $task = new Task();
        $task_data = json_decode($request->getContent(), true);

        $task->setUser($user);
        $task->setLabel($task_data['label']);
        $task->setDescription($task_data['description']);
        $task->setTags($task_data['tags']);

        // As new task, by default task is unchecked
        $task->setChecked(false);
        $task->setDifficulty($task_data['difficulty']);

        $em->persist($task);
        $em->flush();

        return $this->json([
            'success' => (bool)$task,
        ]);
    }

    #[Route('/api/task', name: 'app_task_all', methods: ['GET'])]
    public function all(): JsonResponse
    {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();
        $tasks = $user->getTasks();

        return $this->json([
            'data' => $tasks,
            'success' => true,
        ]);
    }

    #[Route('/api/task/{task_id}', name: 'app_task_show', methods: ['GET'])]
    public function show(int $task_id, EntityManagerInterface $em)
    {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();

        /** @@var App\Repository\TaskRepository $task_repo */
        $task_repo = $em->getRepository(Task::class);

        $task = $task_repo->findOneByIdAndUser($task_id, $user);

        return $this->json([
            'data' => $task,
            'success' => (bool)$task,
        ]);
    }

    #[Route('/api/task/check/{task_id}', name: 'app_task_check', methods: ['GET'])]
    public function check(int $task_id, EntityManagerInterface $em, ExperienceManager $exp_manager)
    {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();

        /** @@var App\Repository\TaskRepository $task_repo */
        $task_repo = $em->getRepository(Task::class);

        $task = $task_repo->findOneByIdAndUser($task_id, $user);
        if (!$task) {
            return $this->json([
                'error' => [
                    'code' => 'task_not_found',
                    'message' => 'Task not found for current user',
                ]
            ]);
        }

        $task->setChecked(true);
        $level = $exp_manager->addExp($user, Task::BASE_EXPERIENCE * $task->getDifficulty());
        $em->flush();

        return $this->json([
            'level' => $level,
            'success' => (bool)$task,
        ]);
    }

    #[Route('/api/task/{task_id}', name: 'app_task_edit', methods: ['PUT'])]
    public function update(int $task_id, EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();

        /** @@var App\Repository\TaskRepository $task_repo */
        $task_repo = $em->getRepository(Task::class);
        $task = $task_repo->findOneByIdAndUser($task_id, $user);
        $task_data = json_decode($request->getContent(), true);

        $task->setLabel($task_data['label']);
        $task->setDescription($task_data['description']);
        $task->setTags($task_data['tags']);

        $em->flush();

        return $this->json([
            'data' => $task,
            'success' => (bool)$task
        ]);
    }

    #[Route('/api/task/{task_id}', name: 'app_task_delete', methods: ['DELETE'])]
    public function delete(int $task_id, EntityManagerInterface $em, Request $request): JsonResponse
    {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();

        /** @@var App\Repository\TaskRepository $task_repo */
        $task_repo = $em->getRepository(Task::class);
        $task = $task_repo->findOneByIdAndUser($task_id, $user);

        if ($task) {
            $em->remove($task);
            $em->flush();
        }

        return $this->json([
            'success' => (bool)$task,
        ]);
    }
}