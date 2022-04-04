<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SecurityController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login')]
    public function apiLogin(): Response
    {
        $user = $this->getUser();

        return new Response([
            'username' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }

    #[Route('/api/register', name: 'api_register', methods: 'POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function register(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, Request $request, SerializerInterface $serializer): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $json = $request->getContent();

        $user = $serializer->deserialize($json, User::class, 'json');
        $plaintextPassword = $user->getPassword();

        $user->setPassword($passwordHasher->hashPassword($user, $plaintextPassword));

        $em->persist($user);
        $em->flush();

        return $this->json([
            'status' => 201,
            'message' => "L'utilisateur a bien été ajouté"
        ], 201);
    }
}
