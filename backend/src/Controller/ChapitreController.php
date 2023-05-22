<?php

namespace App\Controller;

use App\Entity\Chapitre;
use App\Entity\Cours;
use App\Repository\ChapitreRepository;
use App\Repository\CoursRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api', name: 'api_')]
class ChapitreController extends AbstractController
{
    #[Route('/chapitre', name: 'app_chapitre')]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function index(ChapitreRepository $chapitreRepository): Response
    {
        $chapitres = $chapitreRepository->findAll();

        $data = [];

        foreach ($chapitres as $chapitre) {
            $chapitreData = [
                'id' => $chapitre->getId(),
                'name' => $chapitre->getName(),
                'fichier' => $chapitre->getFichier(),
                'dateCreation' => $chapitre->getDateAjout(),
                'cours' => [
                    'id' => $chapitre->getCours()->getId(),
                    'nom' => $chapitre->getCours()->getName(),
                    'description' => $chapitre->getCours()->getDescription(),
                    'image' => $chapitre->getCours()->getImages()
                ],
            ];

            $data[] = $chapitreData;
        }

        return $this->json($data);
    }

    #[Route('/chapitre/{id}', name: 'chapitre', methods:['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function index_chapitre(Chapitre $chapitre): Response
    {
        if (!$chapitre) {
            return $this->json(['error' => 'chapitre not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
                'id' => $chapitre->getId(),
                'name' => $chapitre->getName(),
                'fichier' => $chapitre->getFichier(),
                'dateCreation' => $chapitre->getDateAjout(),
                'cours' => [
                        'id' => $chapitre->getCours()->getId(),
                        'nom' => $chapitre->getCours()->getName(),
                        'description' => $chapitre->getCours()->getDescription(),
                        'image' => $chapitre->getCours()->getImages()
                    ]
                ];

        return $this->json($data, 200);
    }


    #[Route('/chapitre/cours/{id}', name: 'app_chapitre_cours.24', methods:['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function index_cours(Cours $cours, ChapitreRepository $chapitreRepository): Response
    {
        if (!$cours) {
            return $this->json(['error' => 'Cours not found'], Response::HTTP_NOT_FOUND);
        }
        $chapitres = $chapitreRepository->findByCours($cours);

        $data = [];

        foreach ($chapitres as $chapitre) {
            $chapitreData = [
                'id' => $chapitre->getId(),
                'name' => $chapitre->getName(),
                'fichier' => $chapitre->getFichier(),
                'dateCreation' => $chapitre->getDateAjout(),
                'cours' => [
                    'id' => $chapitre->getCours()->getId(),
                    'nom' => $chapitre->getCours()->getName(),
                    'description' => $chapitre->getCours()->getDescription(),
                    'image' => $chapitre->getCours()->getImages()
                ],
            ];

            $data[] = $chapitreData;
        }

        return $this->json($data);
    }

    #[Route('/chapitre/add', name: 'add_chapitre', methods: ['POST' ,'GET'])]
    #[IsGranted('ROLE_ADMIN')]
    #[IsGranted('ROLE_USER')]
    public function addChapitre(Request $request, ValidatorInterface $validator, EntityManagerInterface $entityManager, CoursRepository $coursRepository): Response
    {
        $chapitre = new Chapitre();

        // Récupérez les données de la requête
        $data = $request->request->all();
        $file = $request->files->get('fichier');
        // Validez les données
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank()],
            'cours' => [new Assert\NotBlank()]
        ]);
        $badRequest=null;
        $errors = [];

        $violations = $validator->validate($data, $constraints);
        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[$propertyPath] = $violation->getMessage();
            }
            $badRequest= Response::HTTP_BAD_REQUEST;
        }

        // Récupérez le cours associé au chapitre
        $coursId = $data['cours'];
        $cours = $coursRepository->find($coursId);

        if (!$cours) {
            return $this->json(['error' => 'Cours not found'], Response::HTTP_NOT_FOUND);
        }

        // Créez le chapitre en utilisant les données fournies
        $chapitre->setName($data['name']);
        $chapitre->setCours($cours);
        $chapitre->setDateAjout(new \DateTime('now'));

        // Gérez le téléchargement du fichier PDF
        if ($file instanceof UploadedFile) {
            $fileName = uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir').'/public/fichier', $fileName);
            $chapitre->setFichier($fileName);
        } else {
            $badRequest= Response::HTTP_BAD_REQUEST;
            $errors['fichier']= ['fichier' => 'Fichier is required.'];
        }

        if ($badRequest) {
            return $this->json(['errors' => $errors], $badRequest);
        }

        // Persistez et enregistrez le chapitre dans la base de données
        $entityManager->persist($chapitre);
        $entityManager->flush();

        return $this->json(['message' => 'Chapitre ajouté avec succès'], Response::HTTP_CREATED);
    }

    #[Route('/chapitre/{id}', name: 'edit_cchapitre', methods: ['PUT' ,'PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function editChapitre(Request $request, Chapitre $chapitre, CoursRepository $coursRepository, EntityManagerInterface $entityManager): Response
    {

        if (!$chapitre) {
            return $this->json(['error' => 'not found this chapiter'], Response::HTTP_NOT_FOUND);
        }

        // Récupérer les données de la requête
        $data = $request->request->all();
        $file = $request->files->get('fichier');

        // Récupérer le cours associé au chapitre
        if ($data['cours']) {
            $coursId = $data['cours'];
            $cours = $coursRepository->find($coursId);
            if (!$cours) {
                return $this->json(['error' => 'Chapitre not found'], Response::HTTP_NOT_FOUND);
            }
            $chapitre->setCours($cours);
        }

        // Mettre à jour le chapitre avec les données fournies
        $data['name']?$chapitre->setName($data['name']):$chapitre->setName($chapitre->getName());
        $chapitre->setDateAjout($chapitre->getDateAjout());

        // Gérer le téléchargement du fichier PDF
        if ($file instanceof UploadedFile) {
            $fileName = uniqid().'.'.$file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir').'/public/fichier', $fileName);
            $chapitre->setFichier($fileName);
        }else {
            $chapitre->setFichier($chapitre->getFichier());
        }

        // Enregistrer les modifications du chapitre
        $entityManager->flush();

        return $this->json(['message' => 'Chapitre updated successfully'], Response::HTTP_OK);
    }


    #[Route('/chapitre/{id}', name: 'delete_chapitre', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteChapitre(Chapitre $chapitre, EntityManagerInterface $entityManager): Response
    {
        if (!$chapitre) {
            return $this->json(['error' => 'Chapitre not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($chapitre);
        $entityManager->flush();

        return $this->json(['message' => 'Chapitre deleted successfully']);
    }

}
