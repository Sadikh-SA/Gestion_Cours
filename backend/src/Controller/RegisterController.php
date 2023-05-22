<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/api', name: 'api_')]
class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: "POST")]
    public function register(Request $request, UserPasswordHasherInterface $passwordEncoder, ValidatorInterface $validator, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        // Valider les données
        $user = new Utilisateur();
        $user->setEmail($data['username']);
        $user->setName($data['name']);
        $user->setPassword($data['password']);
        $user->setDateCreation(new \DateTime('now'));
        // Encoder le mot de passe
        $encodedPassword = $passwordEncoder->hashPassword($user, $user->getPassword());
        $user->setPassword($encodedPassword);
        isset($data['role'])? $user->setRoles([$data['role']]) : $user->setRoles(['ROLE_USER']);

        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], 400);
        }

        // Enregistrer l'utilisateur
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json(['message' => 'User registered successfully'], 201);
    }

    #[Route('/users', name: 'users_')]
    #[IsGranted('ROLE_ADMIN')]
    public function liste(UtilisateurRepository $utilisateurRepository): Response
    {
        $utilisateurs = $utilisateurRepository->findAll();
        $data = [];
    
            foreach ($utilisateurs as $user) {
                $userData = [
                    'id' => $user->getId(),
                    'name' => $user->getNote(),
                    'username' => $user->getEmail(),
                    'dateCreation' => $user->getDateCreation(),
                    'role' => $user->getRoles()
                ];
    
                $data[] = $userData;
            }
        return $this->json($data, 200);
    }

    #[Route('/users/{id}', name: 'users_ide', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function user(Utilisateur $user): Response
    {
        if (!$user) {
            return $this->json(['errors' => 'User Not Found'], Response::HTTP_BAD_REQUEST);
        }
        $data = [
                    'id' => $user->getId(),
                    'name' => $user->getNote(),
                    'username' => $user->getEmail(),
                    'dateCreation' => $user->getDateCreation(),
                    'role' => $user->getRoles()
                ];
        return $this->json($data, 200);
    }

    #[Route('/users/{id}', name: 'app_edit_user', methods: ['POST', 'PUT', 'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editer(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $roles = $request->request->get('roles');

        // Mettre à jour les attributs de l'utilisateur existant avec les nouvelles valeurs
        $utilisateur->setName($name);
        $utilisateur->setEmail($email);
        $utilisateur->setPassword($password);
        $utilisateur->setRoles([$roles]);

        // Valider l'objet Utilisateur
        $errors = $validator->validate($utilisateur);

        if (count($errors) > 0) {
            // S'il y a des erreurs de validation, retourner une réponse JSON avec les erreurs
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }

            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();
        return $this->json(['message' => 'Utilisateur éditer']);
    }

    #[Route('/users/{id}', name: 'app_delete_user', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function supprimer(EntityManagerInterface $entityManager, Utilisateur $utilisateur): Response
    {
        if ($utilisateur) {
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->json(['message' => 'Utilisateur supprimer']);
    }

}
