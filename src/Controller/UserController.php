<?php
// src/Controller/UserController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    #[Route('/create-users', name: 'create_users')]
    public function createUsers(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Supprimer tous les utilisateurs existants pour éviter les conflits
        $existingUsers = $entityManager->getRepository(User::class)->findAll();
        foreach ($existingUsers as $user) {
            $entityManager->remove($user);
        }
        $entityManager->flush();

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $passwordHasher->hashPassword($user, 'user123');
        $user->setPassword($hashedPassword);

        $entityManager->persist($admin);
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'message' => 'Utilisateurs recréés avec succès',
            'admin' => [
                'email' => 'admin@example.com',
                'password' => 'admin123',
                'hash' => substr($admin->getPassword(), 0, 20) . '...'
            ],
            'user' => [
                'email' => 'user@example.com', 
                'password' => 'user123',
                'hash' => substr($user->getPassword(), 0, 20) . '...'
            ]
        ]);
    }

    #[Route('/debug-users', name: 'debug_users')]
    public function debugUsers(EntityManagerInterface $entityManager): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        
        $userInfo = [];
        foreach ($users as $user) {
            $userInfo[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'password_hash' => substr($user->getPassword(), 0, 20) . '...',
            ];
        }
        
        return $this->json([
            'users_count' => count($users),
            'users' => $userInfo,
            'all_emails' => array_map(fn($u) => $u->getEmail(), $users)
        ]);
    }
    
#[Route('/create-debug-users', name: 'create_debug_users')]
public function createDebugUsers(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    try {
        // Supprimer tous les utilisateurs existants
        $existingUsers = $entityManager->getRepository(User::class)->findAll();
        $deletedCount = count($existingUsers);
        
        foreach ($existingUsers as $user) {
            $entityManager->remove($user);
        }
        $entityManager->flush();

        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        
        // Hash du mot de passe
        $plainPassword = '123';
        $hashedPassword = $passwordHasher->hashPassword($admin, $plainPassword);
        $admin->setPassword($hashedPassword);

        // Persist et flush
        $entityManager->persist($admin);
        $entityManager->flush();
        
        // Vérifier que l'utilisateur est bien créé
        $createdAdmin = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@test.com']);
        $passwordTest = $createdAdmin ? $passwordHasher->isPasswordValid($createdAdmin, $plainPassword) : false;

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@test.com');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword2 = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword2);

        $entityManager->persist($user);
        $entityManager->flush();

        // Vérifier tous les utilisateurs créés
        $allUsers = $entityManager->getRepository(User::class)->findAll();

        return $this->json([
            'success' => true,
            'deleted_users' => $deletedCount,
            'created_users' => count($allUsers),
            'admin_created' => $createdAdmin !== null,
            'admin_password_valid' => $passwordTest,
            'users' => array_map(function($u) {
                return [
                    'id' => $u->getId(),
                    'email' => $u->getEmail(),
                    'roles' => $u->getRoles()
                ];
            }, $allUsers),
            'credentials' => [
                'admin' => ['email' => 'admin@test.com', 'password' => '123'],
                'user' => ['email' => 'user@test.com', 'password' => '123']
            ]
        ]);
        
    } catch (\Exception $e) {
        return $this->json([
            'error' => true,
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
}

#[Route('/test-password', name: 'test_password')]
public function testPassword(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur admin
    $admin = $entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@test.com']);
    
    if (!$admin) {
        return $this->json(['error' => 'Admin user not found']);
    }
    
    // Tester si le mot de passe correspond
    $isValid = $passwordHasher->isPasswordValid($admin, '123');
    
    return $this->json([
        'admin_exists' => true,
        'password_test' => $isValid,
        'stored_hash' => substr($admin->getPassword(), 0, 30) . '...',
        'user_identifier' => $admin->getUserIdentifier(),
        'roles' => $admin->getRoles()
    ]);
}
}