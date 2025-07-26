<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger selon son rôle
        if ($this->getUser()) {
            return $this->redirectBasedOnRole();
        }

        // Récupère les erreurs d'authentification
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route("/logout", name: "app_logout")]
    public function logout(): void
    {
        // Cette méthode peut être vide - elle sera interceptée par la clé logout de votre firewall
    }

    /**
     * Redirige l'utilisateur selon son rôle
     */
    private function redirectBasedOnRole(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_home');
        } else {
            return $this->redirectToRoute('home'); // ou 'user_dashboard' si vous en créez un
        }
    }
}