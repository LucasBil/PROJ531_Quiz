<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Entity\Answer;

class ScoreController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $doctrine,
        private Security $security,
    ) {}

    public function mean_scores($answers) {
        $sum = 0;
        foreach ($answers as $answer) {
            // $sum += $answer->getScore();
            $sum += $answer["score"];
        }
        return $sum / count($answers);
    }


    #[Route('/score', name: 'app_score')]
    public function index(): Response {
        $user = $this->security->getUser();

        $answerRepository = $this->doctrine->getRepository(Answer::class);
        $user_answers = $answerRepository->getUserAnswersWithJoinedQuiz($user->getId());

        return $this->render('score/index.html.twig', [
            'controller_name' => 'ScoreController',
            'score' => $this->mean_scores($user_answers),
            'time' => "Type à changer dans la bdd",
            'answers' => $user_answers,
        ]);
    }
}
