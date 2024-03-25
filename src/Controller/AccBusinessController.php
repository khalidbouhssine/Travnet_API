<?php

namespace App\Controller;

use App\Entity\AccBusiness;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccBusinessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/accBusiness', name: 'accBusiness', methods: ['POST'])]
    public function createUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Validate incoming JSON data
        if (!$data || !isset($data['firstName'], $data['lastName'], $data['email'], $data['phone'], $data['cinOrPassport'], $data['role'], $data['nationality'], $data['password'])) {
            return $this->json(['stateData' => 0], 400);
        }

        $user = new AccBusiness();
        $user->setProId(generateUserId());
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);
        $user->setPhone($data['phone']);
        $user->setCinOrPassport($data['cinOrPassport']);
        $user->setRole($data['role']);
        $user->setNationality($data['nationality']);
        $user->setPassword($data['password']);
        $user->setValide(0);
        $user->setBlock(0);

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json([
                'stateData' => 1,
                'stateStore' => 1,
                'userId' => $user->getId()
            ]);
        } catch (\Exception $e) {
            // Return JSON response with error message
            return $this->json([
                'stateData' => 1,
                'stateStore' => 0
            ]);
        }
    }

    
}

function generateUserId() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $userId = '';
    $length = 25;

    for ($i = 0; $i < $length; $i++) {
        $randomIndex = rand(0, strlen($characters) - 1);
        $userId .= $characters[$randomIndex];
    }

    return $userId;
}
