<?php

namespace App\Controller;

use App\Entity\AccClient; // Assuming your entity class is AccClient
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccClientController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/acc_client', name: 'acc_client', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate incoming JSON data
        if (!$data || !isset($data['fullName'], $data['email'], $data['phone'], $data['password'], $data['valide'], $data['block'])) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }

        // Create new AccClient entity and set its properties
        $user = new AccClient();
        $user->setFullName($data['fullName']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setPassword($data['password']);
        $user->setValide($data['valide']);
        $user->setBlock($data['block']);

        // Persist and flush the entity
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // Return JSON response with success message and user ID
        return $this->json([
            'state' => 1,
            'userId' => $user->getId()
        ]);
    }
}
