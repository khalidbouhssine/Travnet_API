<?php

namespace App\Controller;

use Symfony\Component\Mailer\Exception\TransportExceptionInterface; // Correction #1
use DateTime;
use App\Entity\Validation;
use App\Entity\AccClient;
use App\Entity\AccBusiness;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport; // Correction #2
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport; // Correction #2

class ValidationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {   
        $this->entityManager = $entityManager;
    }

    // Create a function that will generate a six-digit number
    private function generateRandomSixDigitNumber(): int
    {
        return random_int(100000, 999999);
    }

    #[Route('/validationEmail', name: 'validationEmail', methods: ['POST'])]
    public function createUser(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        if (!$data || !isset($data['email'])) {
            return $this->json(['error' => 'Invalid JSON data'], 400);
        }
    
        $emailExistsInAccBusiness = $this->entityManager->getRepository(AccBusiness::class)->findOneBy(['email' => $data['email']]);
        $emailExistsInAccClient = $this->entityManager->getRepository(AccClient::class)->findOneBy(['email' => $data['email']]);
        //verfication est ce que le tableau se trouve deja dans le tableau validation 
        $emailExistsInValidation = $this->entityManager->getRepository(Validation::class)->findOneBy(['email' => $data['email']]);
    
        if ($emailExistsInAccBusiness || $emailExistsInAccClient) {
            try {
                $entity = $emailExistsInAccBusiness ?? $emailExistsInAccClient;
                // Get the value of the "valide" attribute
                $valide = $entity->getValide();
                if ($valide) {
                    if($emailExistsInAccBusiness){
                        return $this->json([
                            'exist' => 1,
                            'valide' => 1,
                            'id'=> $entity->getId(),
                            'email'=> $data['email'],
                            'business'=> 1,
                            'password'=>$entity->getPassword(),
                        ]);  
                    }else{
                        return $this->json([
                            'exist' => 1,
                            'valide' => 1,
                            'id'=> $entity->getId(),
                            'email'=> $data['email'],
                            'business'=> 0,
                            'password'=>$entity->getPassword(),
                        ]);
                    }
                } else {
                    if ($emailExistsInValidation) {
                        // Update existing validation record
                        $randomNumber = $this->generateRandomSixDigitNumber();
                        $currentDate = new DateTime();
                        $emailExistsInValidation->setNumDeValidation($randomNumber);
                        $emailExistsInValidation->setDateDeSaveNumValidation($currentDate);
                        $this->entityManager->flush();
            
                        // Send email with updated random number
                        try {
                            $transport = new EsmtpTransport('smtp.gmail.com', 587);
                            $transport->setUsername('travnetcompany@gmail.com');
                            $transport->setPassword('ekyjhmqjnmnvcedk');
                            $mailer = new Mailer($transport);
                            $email = (new Email())
                                ->from('travnetcompany@gmail.com')
                                ->to($data['email'])
                                ->subject('Validation Code')
                                ->text('Your updated validation code: ' . $randomNumber);
                            $mailer->send($email);
                            return $this->json([
                                'exist' => 1,
                                'stateCode' => 1,
                                'valide'=>0,
                            ]);
                        } catch (TransportExceptionInterface $e) {
                            return $this->json([
                                'state' => 0
                            ]);
                        }
                    } else {
                        try {
                            $randomNumber = $this->generateRandomSixDigitNumber();
                            $currentDate = new DateTime();
                            // Create new Validation entity and set its properties
                            $validation = new Validation();
                            $validation->setEmail($data['email']);
                            $validation->setNumDeValidation($randomNumber);
                            $validation->setDateDeSaveNumValidation($currentDate);
                            $this->entityManager->persist($validation);
                            $this->entityManager->flush();
                            // Send email with random number
                            $transport = new EsmtpTransport('smtp.gmail.com', 587);
                            $transport->setUsername('travnetcompany@gmail.com');
                            $transport->setPassword('ekyjhmqjnmnvcedk');
                            $mailer = new Mailer($transport);
                            $email = (new Email())
                                ->from('travnetcompany@gmail.com')
                                ->to($data['email'])
                                ->subject('Validation Code')
                                ->text('Your validation code: ' . $randomNumber);
                            $mailer->send($email);
                            return $this->json([
                                'exist' => 1,
                                'state' => 1,
                            ]);
                        } catch (TransportExceptionInterface $e) {
                            return $this->json([
                                'state' => 0
                            ]);
                        }
                    }
                }
            } catch (TransportExceptionInterface $e) {
                // Return JSON response with error message
                return $this->json([
                    'state' => 0
                ]);
            }
        } else {
            return $this->json(['exist' =>0], 200);
        }
    }

    // route pour valider le code qui deja generer
    #[Route('/codevalidation', name: 'codevalidation', methods: ['POST'])]
    public function validateCodeUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!$data || !isset($data['email'],$data['numDeValidation'])) {
            return $this->json(['error' => 'Invalid JSON data'], 200);
        }
        $email = $data['email'];
        $EmailIsExist = $this->entityManager->getRepository(Validation::class)->findOneBy(['email' => $email]);
        $codeRecuperer= $EmailIsExist->getNumDeValidation();
        //partie pour identifier email appartient a quelle classe pour changer la valeur de attribut valide
        $emailExistsInAccBusiness = $this->entityManager->getRepository(AccBusiness::class)->findOneBy(['email' => $data['email']]);
        $emailExistsInAccClient = $this->entityManager->getRepository(AccClient::class)->findOneBy(['email' => $data['email']]);
        $entity = $emailExistsInAccBusiness ?? $emailExistsInAccClient;
        /////////////////////////////////
        if ($EmailIsExist) {
            if($codeRecuperer==$data['numDeValidation']){
                $valide = $entity->setValide(true);
                $this->entityManager->remove($EmailIsExist);
                $this->entityManager->flush();
                return $this->json([
                    'exist' => 1,
                    'comparaison' => 1,
                    'valide' => 1
                ]);
            }else{
                return $this->json([
                    'exist' => 1,
                    'comparaison' => 0,
                    'valide' => 0
                ]);
            }
           
        } else {
            return $this->json([
                'exist' => 1
            ]);
        }
    }
}