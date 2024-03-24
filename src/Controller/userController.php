<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class userController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/create-user', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Récupération des données nécessaires pour créer un nouvel utilisateur
        $nom = $data['nom'] ?? 'John Doe'; // Valeur par défaut pour le nom
        $age = $data['age'] ?? 30; // Valeur par défaut pour l'âge

        // Création d'une nouvelle instance de l'entité User
        $user = new User();
        $user->setNom($nom);
        $user->setAge($age);

        // Persistance de l'utilisateur dans la base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'User created successfully',
            'userId' => $user->getId()
        ]);
    }

    #[Route('/users', name: 'list_users', methods: ['GET'])]
    public function listUsers(): JsonResponse
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'id' => $user->getId(),
                'nom' => $user->getNom(),
                'age' => $user->getAge(),
            ];
        }

        return $this->json($userData);
    }
    
}