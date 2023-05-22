<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Entity\Notes;
use App\Repository\CoursRepository;
use App\Repository\NotesRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api', name: 'api_')]
class NotesController extends AbstractController
{
    #[Route('/notes', name: 'app_notes')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(NotesRepository $notesRepository): JsonResponse
    {
        $notes = $notesRepository->findAll();
        $data = [];
    
            foreach ($notes as $note) {
                $noteData = [
                    'id' => $note->getId(),
                    'note' => $note->getNote(),
                    'dateNote' => $note->getDateNote(),
                    'cours' => [
                        'id' => $note->getCours()->getId(),
                        'nom' => $note->getCours()->getName(),
                        'description' => $note->getCours()->getDescription(),
                        'image' => $note->getCours()->getImages()
                    ],
                    'users' => [
                        'id' => $note->getUsers()->getId(),
                        'nom' => $note->getUsers()->getName(),
                        'username' => $note->getUsers()->getEmail(),
                        'dateCreation' => $note->getUsers()->getDateCreation(),
                        'role' => $note->getUsers()->getRoles()
                    ],
                ];
    
                $data[] = $noteData;
            }
        return $this->json($data, 200);
    }

    #[Route('/notes/{nom}/{cours}', name: 'app_chapitre_cours')]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function index_cours(String $nom, Cours $cours, NotesRepository $notesRepository): Response
    {
        $data = [];
        if ($nom=="users") {
            if (!$cours) {
                return $this->json(['error' => 'Cours not found'], Response::HTTP_NOT_FOUND);
            }
            $notes = $notesRepository->findByUsers($cours);
    
            foreach ($notes as $note) {
                $noteData = [
                    'id' => $note->getId(),
                    'note' => $note->getNote(),
                    'dateNote' => $note->getDateNote(),
                    'cours' => [
                        'id' => $note->getCours()->getId(),
                        'nom' => $note->getCours()->getName(),
                        'description' => $note->getCours()->getDescription(),
                        'image' => $note->getCours()->getImages()
                    ],
                    'users' => [
                        'id' => $note->getUsers()->getId(),
                        'nom' => $note->getUsers()->getName(),
                        'username' => $note->getUsers()->getEmail(),
                        'dateCreation' => $note->getUsers()->getDateCreation(),
                        'role' => $note->getUsers()->getRoles()
                    ],
                ];
    
                $data[] = $noteData;
            }
        }else {
            if (!$cours) {
                return $this->json(['error' => 'Cours not found'], Response::HTTP_NOT_FOUND);
            }
            $notes = $notesRepository->findByCours($cours);
    
            foreach ($notes as $note) {
                $noteData = [
                    'id' => $note->getId(),
                    'note' => $note->getNote(),
                    'dateNote' => $note->getDateNote(),
                    'cours' => [
                        'id' => $note->getCours()->getId(),
                        'nom' => $note->getCours()->getName(),
                        'description' => $note->getCours()->getDescription(),
                        'image' => $note->getCours()->getImages()
                    ],
                    'users' => [
                        'id' => $note->getUsers()->getId(),
                        'nom' => $note->getUsers()->getName(),
                        'username' => $note->getUsers()->getEmail(),
                        'dateCreation' => $note->getUsers()->getDateCreation(),
                        'role' => $note->getUsers()->getRoles()
                    ],
                ];
    
                $data[] = $noteData;
            }
        }

        return $this->json($data);
    }

    #[Route('/notes/add', name: 'add_notes', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function addNotes(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator, CoursRepository $coursRepository): Response
    {
        $data = $request->request->all();

        // Validez les données
        $constraints = new Assert\Collection([
            'note' => [new Assert\NotBlank()],
            'cours' => [new Assert\NotBlank()]
        ]);

        $violations = $validator->validate($data, $constraints);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[$propertyPath] = $violation->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
        // Créez une nouvelle instance de Notes
        $note = new Notes();
        $note->setDateNote(new DateTime('now'));
        $note->setNote($data['note']);
        $note->setUsers($this->getUser());
        // Récupérez le cours associé au chapitre
        $coursId = $data['cours'];
        $cours = $coursRepository->find($coursId);

        if (!$cours) {
            return $this->json(['error' => 'Cours not found'], Response::HTTP_NOT_FOUND);
        }
        $note->setCours($cours);

        // Enregistrez le cours dans la base de données
        $entityManager->persist($note);
        $entityManager->flush();

        // Retournez une réponse JSON
        return $this->json(['message' => 'Note added successfully'], Response::HTTP_CREATED);

    }
}
