<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');
            $role = $request->request->get('role');

            $validRoles = ['ROLE_R_FINANCIER', 'ROLE_ARTISAN', 'ROLE_USER'];

            if (!in_array($role, $validRoles)) {
                $this->addFlash('error', 'Rôle invalide.');
                return $this->redirectToRoute('app_register');
            }

            if (empty($email) || empty($password) || empty($passwordConfirm)) {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
                return $this->redirectToRoute('app_register');
            }

            if ($password !== $passwordConfirm) {
                $this->addFlash('error', 'Les mots de passe ne correspondent pas.');
                return $this->redirectToRoute('app_register');
            }

            $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existingUser) {
                $this->addFlash('error', 'Un compte avec cet email existe déjà.');
                return $this->redirectToRoute('app_register');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setPassword($passwordHasher->hashPassword($user, $password));
            $user->setRoles([$role]);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a été créé avec succès !');
            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register2.html.twig', [
            'roles' => ['ROLE_R_FINANCIER', 'ROLE_ARTISAN', 'ROLE_USER'],
        ]);
    }
}