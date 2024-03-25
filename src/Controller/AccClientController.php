<?php

namespace App\Controller;

use App\Entity\AccClient;
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

    #[Route('/accClient', name: 'acc_client', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate incoming JSON data
        if (!$data || !isset($data['fullName'], $data['email'], $data['phone'], $data['password'])) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }

        // Create new AccClient entity and set its properties
        $user = new AccClient();
        $user->setFullName($data['fullName']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setPassword($data['password']);
        $user->setValide(0);
        $user->setBlock(0);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json([
                'state' => 1,
                'userId' => $user->getId()
            ]);
        } catch (\Exception $e) {
            // Return JSON response with error message
            return $this->json([
                'state' => 0
            ]);
        }
    }
}
