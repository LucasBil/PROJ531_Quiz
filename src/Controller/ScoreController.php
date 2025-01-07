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
        if (count($answers) == 0) {
            return 0;
        }

        $sum = 0;
        foreach ($answers as $answer) {
            $sum += $answer->getScore() / $answer->getQuiz()->getMaxScore();
        }
        return $sum * 10 / count($answers);
    }

    public function mean_time($answers) {
        if (count($answers) == 0) {
            return 0;
        }

        $defautlDateTime = new \DateTime();
        $totalTime = new \DateTime();

        foreach ($answers as $answer) {
            $totalTime->add($answer->getTime());
        }
        $seconds = $totalTime->getTimestamp() - $defautlDateTime->getTimestamp();
        $seconds = (int) ($seconds / count($answers));

        return \DateInterval::createFromDateString("$seconds seconds");
    }


    #[Route('/score', name: 'app_score')]
    public function index(): Response {
        $user = $this->security->getUser();
        
        $answerRepository = $this->doctrine->getRepository(Answer::class);
        $user_answers = $answerRepository->getUserAnswersWithJoinedQuiz($user);

        return $this->render('score/index.html.twig', [
            'controller_name' => 'ScoreController',
            'score' => $this->mean_scores($user_answers),
            'time' => $this->mean_time($user_answers),
            'answers' => $user_answers,
        ]);
    }
}
