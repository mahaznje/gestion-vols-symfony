<?php
// src/Controller/AdminController.php

namespace App\Controller;

use App\Entity\Vol;
use App\Entity\Ville;
use App\Form\VolType;
use App\Form\VilleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Route pour la page d'accueil de l'administration
    #[Route('/admin', name: 'admin_home')]
    public function index(): Response
    {
        // Vérifie si l'utilisateur est un admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $vols = $this->entityManager->getRepository(Vol::class)->findAll();
        return $this->render('admin/index.html.twig', [
            'vols' => $vols,
        ]);
    }

    // Route pour ajouter un nouveau vol
    #[Route('/admin/vol/new', name: 'admin_new_vol')]
    public function newVol(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $vol = new Vol();
        $form = $this->createForm(VolType::class, $vol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer un numéro de vol aléatoire
            $vol->setNumero($this->generateFlightNumber());

            // Enregistrer le vol dans la base de données
            $this->entityManager->persist($vol);
            $this->entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Vol ajouté avec succès !');

            // Rediriger vers la page de gestion des vols après l'ajout
            return $this->redirectToRoute('admin_home');
        }

        // Retourner le formulaire à la vue
        return $this->render('admin/new_vol.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour générer un numéro de vol aléatoire
    private function generateFlightNumber(): string
    {
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $digits = '0123456789';
        
        // Générer un numéro de vol composé de 2 lettres suivies de 4 chiffres
        $flightNumber = '';
        
        // 2 lettres aléatoires
        for ($i = 0; $i < 2; $i++) {
            $flightNumber .= $letters[rand(0, strlen($letters) - 1)];
        }
        
        // 4 chiffres aléatoires
        for ($i = 0; $i < 4; $i++) {
            $flightNumber .= $digits[rand(0, strlen($digits) - 1)];
        }
        
        return $flightNumber;
    }

    // Route pour modifier un vol
    #[Route('/admin/vol/{id}/edit', name: 'admin_edit_vol')]
    public function editVol(Request $request, Vol $vol): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        // Créer le formulaire avec les données existantes du vol
        $form = $this->createForm(VolType::class, $vol);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour le vol dans la base de données
            $this->entityManager->flush();

            // Ajouter un message flash de succès
            $this->addFlash('success', 'Vol modifié avec succès !');

            // Rediriger vers la page de gestion des vols
            return $this->redirectToRoute('admin_home');
        }

        // Rendu du formulaire
        return $this->render('admin/edit_vol.html.twig', [
            'form' => $form->createView(),
            'vol' => $vol,
        ]);
    }

    // Route pour supprimer un vol
    #[Route('/admin/vol/{id}/delete', name: 'admin_delete_vol')]
    public function deleteVol(Request $request, Vol $vol): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        // Vérification du token CSRF pour la sécurité
        if ($this->isCsrfTokenValid('delete' . $vol->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($vol);
            $this->entityManager->flush();

            // Ajouter un message flash de succès après la suppression
            $this->addFlash('success', 'Vol supprimé avec succès !');
        }

        // Rediriger vers la page de gestion des vols
        return $this->redirectToRoute('admin_home');
    }

    #[Route('/admin/ville/new', name: 'admin_new_ville')]
    public function newVille(Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('home');
        }

        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si la ville existe déjà dans la base de données
            $existingVille = $this->entityManager->getRepository(Ville::class)->findOneBy(['nom' => $ville->getNom()]);

            if ($existingVille) {
                // Ajouter un message flash d'erreur si la ville existe déjà
                $this->addFlash('error', 'Cette ville existe déjà dans la base de données.');
                // Renvoyer à la même page avec le message d'erreur
                return $this->render('admin/new_ville.html.twig', [
                    'form' => $form->createView(),
                ]);
            } else {
                // Si la ville n'existe pas, persister et ajouter un message de succès
                $this->entityManager->persist($ville);
                $this->entityManager->flush();
                $this->addFlash('success', 'Ville ajoutée avec succès !');
                // Rediriger vers la page de gestion des vols après l'ajout
                return $this->redirectToRoute('admin_home');
            }
        }

        // Si le formulaire n'a pas encore été soumis ou est invalidé, afficher à nouveau le formulaire
        return $this->render('admin/new_ville.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}