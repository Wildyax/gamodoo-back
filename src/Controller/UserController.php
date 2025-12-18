<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UserController extends AbstractController
{
    #[Route('/users/{user_id}', name: 'user_get', methods: ['GET'])]
    public function get(EntityManagerInterface $em, int $user_id): JsonResponse
    {
        $user = $em->getRepository(User::class)->find($user_id);

        if (!$user) {
            return $this->json([
                'error' => [
                    'code' => 'user_not_found',
                    'message' => "Aucun utilisateur avec l'id $user_id n'a été trouvé"
                ],
            ]);
        }

        return $this->json([
            'user_id' => $user->getId(),
            'user_login' => $user->getLogin(),
            'user_email' => $user->getEmail(),
        ]);
    }

    #[Route('/users', name: 'api_user_list', methods: ['GET'])]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        $users = $em->getRepository(User::class)->findAll();

        $data = array_map(static function (User $user) {
            return [
                'user_id' => $user->getId(),
                'user_login' => $user->getLogin(),
                'user_email' => $user->getEmail(),
            ];
        }, $users);

        return $this->json($data);
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email'], $data['password'])) {
            return $this->json([
                'error' => [
                    'code' => 'login_invalid_payload',
                    'message' => 'Email et mot de passe requis'
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $data['email'],
        ]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json([
                'error' => [
                    'code' => 'login_invalid_credentials',
                    'message' => 'Identifiants invalides'
                ],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'user_id' => $user->getId(),
            'user_login' => $user->getLogin(),
            'user_email' => $user->getEmail(),
        ]);
    }


    #[Route('/users', name: 'user_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setLogin($data['login']);
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
            'user_id' => $user->getId(),
            'user_login' => $user->getLogin(),
            'user_email' => $user->getEmail(),
        ]);
    }

    #[Route('/users/{user_id}', name: 'user_update', methods: ['PUT'])]
    public function update(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        int $user_id
    ): JsonResponse {

        $user = $em->getRepository(User::class)->find($user_id);

        if (!$user) {
            return $this->json([
                'error' => [
                    'code' => 'user_not_found',
                    'message' => "Aucun utilisateur avec l'id $user_id n'a été trouvé"
                ],
            ]);
        }

        $data = json_decode($request->getContent(), true);
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['login'])) {
            $user->setLogin($data['login']);
        }
        if (isset($data['password'])) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $data['password'])
            );
        }

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'user_update_validation',
                    'message' => $errors->get(0)->getMessage()
                ],
            ]);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'user_id' => $user->getId(),
            'user_login' => $user->getLogin(),
            'user_email' => $user->getEmail(),
        ]);
    }

    #[Route('/users/{user_id}', name: 'user_delete', methods: ['DELETE'])]
    public function delete(
        EntityManagerInterface $em,
        int $user_id
    ): JsonResponse {
        $user = $em->getRepository(User::class)->find($user_id);

        if (!$user) {
            return $this->json([
                'error' => [
                    'code' => 'user_not_found',
                    'message' => "Aucun utilisateur avec l'id $user_id n'a été trouvé"
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
