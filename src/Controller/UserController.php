<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    #[Route('/api/register', name: 'api_user_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $job = $em->getRepository(Job::class)->find($data['job']);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setLogin($data['login']);
        $user->setJob($job);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'user_create_validation',
                    'message' => $errors->get(0)->getMessage()
                ],
            ]);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'success' => true
        ]);
    }

    #[Route('/api/user', name: 'api_user_get', methods: ['GET'])]
    public function showUser(): JsonResponse
    {
        /** @var App\Entity\User $user */
        $user = $this->getUser();

        return $this->json($user);
    }

    #[Route('/api/user', name: 'api_user_update', methods: ['POST'])]
    public function updateUser(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {

        /** @@var App\Entity\User $user */
        $user = $this->getUser();
        $data = json_decode($request->getContent(), true);
        $job = $em->getRepository(Job::class)->find($data['job']);

        $user->setEmail($data['email']);
        $user->setLogin($data['login']);
        $user->setJob($job);
        $user->setPassword(
            $passwordHasher->hashPassword($user, $data['password'])
        );

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'user_create_validation',
                    'message' => $errors->get(0)->getMessage()
                ],
            ]);
        }

        return $this->json($user);
    }

    #[Route('/api/user', name: 'api_user_delete', methods: ['DELETE'])]
    public function deleteUser(
        EntityManagerInterface $em,
    ): JsonResponse {
        /** @@var App\Entity\User $user */
        $user = $this->getUser();

        $em->remove($user);
        $em->flush();

        return $this->json([
            'success' => true
        ]);
    }
}
