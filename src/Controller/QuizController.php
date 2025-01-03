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

    #[IsGranted("ROLE_USER")]
    #[Route("/ajax/quiz/send", name:"ajax_quiz_send_answer", methods:["POST"])]
    public function send(): Response
    {
        $QuizRepository = $this->entityManager->getRepository(Quiz::class);
        [
            "quiz" => $idQuiz,
            "answer" => $jsonAnswer,
            "start" => $dateTimeStart,
        ] = $_POST;

        $quiz = $QuizRepository->find($idQuiz);
        $time = (new DateTime("@" . $dateTimeStart/1000))->diff(new DateTime());
        $answer = new Answer();
        $answer->setUser($this->getUser())
            ->setQuiz($quiz)
            ->setDateTime(new DateTime())
            ->setTime($time)
            ->setScore($this->getScoreByJson($jsonAnswer));
        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return new JsonResponse($answer, Response::HTTP_OK);
    }

    private function getScoreByJson(array $jsonAnswer): int
    {
        $score = 0;
        foreach ($jsonAnswer as $question) {
            $coef = Type::from($question["type"]["name"])->makeCoef($question);
            $score += $question["points"] * $coef;
        }
        return $score;
    }
}
