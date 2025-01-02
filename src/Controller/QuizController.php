<?php

namespace App\Controller;

use App\Entity\Quiz;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class QuizController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[IsGranted("ROLE_USER")]
    #[Route("/quiz/{id}", name:"quiz_show", methods:["GET"])]
    public function show(int $id): Response
    {
        // Récupérer le quiz par son ID
        $QuizRepository = $this->entityManager->getRepository(Quiz::class);
        $quiz = $QuizRepository->find($id);

        // Si le quiz n'existe pas, renvoyer une erreur 404
        if (!$quiz) {
            throw $this->createNotFoundException("Le quiz avec l'ID $id n'existe pas.");
        }

        // Récupérer les questions associées
        $questions = $quiz->getQuestions()->toArray();

        // Retourner la réponse avec un rendu Twig
        return $this->render('questionnaire/show.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
        ]);
    }
}
