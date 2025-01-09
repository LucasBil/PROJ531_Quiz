<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Quiz;
use App\Enum\Type;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route("/", name:"quizs", methods:["GET"])]
    public  function showAll() {
        $QuizRepository = $this->entityManager->getRepository(Quiz::class);
        $quizs = $QuizRepository->findAll();

        return $this->render('questionnaire/index.html.twig', [
            'quizs' => $quizs,
        ]);
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

        // Retourner la réponse avec un rendu Twig
        return $this->render('questionnaire/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
