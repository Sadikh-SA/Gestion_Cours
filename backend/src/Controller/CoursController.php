<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api', name: 'api_')]
class CoursController extends AbstractController
{
    #[Route('/cours', name: 'app_cours', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CoursRepository $coursRepository): Response
    {
        $cours = $coursRepository->findAll();
        $data = [];
    
            foreach ($cours as $cour) {
                $noteData = [
                    'id' => $cour->getId(),
                    'nom' => $cour->getName(),
                    'description' => $cour->getDescription(),
                    'images' => $cour->getImages()
                ];
    
                $data[] = $noteData;
            }
        return $this->json($data, 200);
    }

    #[Route('/cours/add', name: 'add_cours', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function addCours(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {

        // Récupérez les données du formulaire
        $data = $request->request->all();
        $imageFile = $request->files->get('image');

        // Validez les données
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank()],
            'description' => [new Assert\NotBlank()]
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

        // Gérez le téléchargement de l'image
        if ($imageFile instanceof UploadedFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('kernel.project_dir').'/public/images',
                $newFilename
            );

            $data['image'] = $newFilename;
        } else {
            return $this->json(['errors' => ['image' => 'Image is required.']], Response::HTTP_BAD_REQUEST);
        }

        // ...

        // Créez une nouvelle instance de Course
        $course = new Cours();
        $course->setName($data['name']);
        $course->setDescription($data['description']);
        $course->setImages($data['image']);

        // Enregistrez le cours dans la base de données
        $entityManager->persist($course);
        $entityManager->flush();

        // Retournez une réponse JSON
        return $this->json(['message' => 'Course added successfully'], Response::HTTP_CREATED);
    
    }

    #[Route('/cours/{id}', name: 'edit_cours', methods: ['PUT' ,'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editCours(Request $request, ValidatorInterface $validator, Cours $course, EntityManagerInterface $entityManager): Response
    {

        if (!$course) {
            return $this->json(['error' => 'Cours not found'], Response::HTTP_NOT_FOUND);
        }

        // Récupérez les données de la requête
        $data = $request->request->all();
        $imageFile = $request->files->get('image');

        // Validez les données
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank()],
            'description' => [new Assert\NotBlank()],
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

        // Gérez le téléchargement de la nouvelle image
        if ($imageFile instanceof UploadedFile) {
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('kernel.project_dir').'/public/images',
                $newFilename
            );

            $course->setImages($newFilename);
        } else {
            return $this->json(['errors' => ['image' => 'Image is required.']], Response::HTTP_BAD_REQUEST);
        }

        // Mettez à jour les autres attributs du cours
        $course->setName($data['name']);
        $course->setDescription($data['description']);

        $entityManager->flush();

        return $this->json(['message' => 'Course updated successfully']);
    }


    #[Route('/cours/{id}', name: 'delete_cours', methods: ['DELETE'])]
    public function deleteCourse(Cours $course, EntityManagerInterface $entityManager): Response
    {
        if (!$course) {
            return $this->json(['error' => 'Course not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($course);
        $entityManager->flush();

        return $this->json(['message' => 'Course deleted successfully']);
    }

}
