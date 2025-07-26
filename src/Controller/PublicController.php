<?php
// src/Controller/PublicController.php
// src/Controller/PublicController.php

namespace App\Controller;

use App\Entity\Vol;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PublicController extends AbstractController
{
    private $entityManager;

    // Injection de l'EntityManager dans le constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        // Utilisation de l'EntityManager pour accéder à la base de données
        $vols = $this->entityManager->getRepository(Vol::class)->findAll();

        return $this->render('public/index.html.twig', [
            'vols' => $vols,
        ]);
    }
}