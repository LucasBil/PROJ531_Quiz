<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\PossibleAnswer;
use App\Entity\Question;
use App\Entity\QuestionType;
use App\Entity\Quiz;
use App\Entity\Theme;
use App\Enum\Difficulty;
use App\Enum\Type;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ajax')]
class AjaxController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $doctrine,
    ) {}

    #[IsGranted("ROLE_ADMIN")]
    #[Route('/quiz/create', name: 'ajax_quiz_create', methods: ['POST'])]
    public function createQuiz(): JsonResponse
    {
        $questionTypeRepository = $this->doctrine->getRepository(QuestionType::class);
        $themeRepository = $this->doctrine->getRepository(Theme::class);

        [
            "questions" => $questions,
            "quiz" => $quiz,
        ] = $_POST;

        $theme = $themeRepository->findOneBy(['name' => $quiz["theme"]]);
        if ($theme == null) {
            $theme = new Theme();
            $theme->setName($quiz["theme"]);
            $this->doctrine->persist($theme);
            $this->doctrine->flush();
        }

        $maxtime = null;
        if ( !empty($quiz["timeout"]) ) {
            $time = explode(':', $quiz["timeout"]);
            $maxtime = DateInterval::createFromDateString("$time[0] hours + $time[1] minutes + $time[2] seconds");
        }

        $difficulty = Difficulty::cases()[$quiz["difficulty"]];

        $quizDoctrine = new Quiz();
        $quizDoctrine->setName($quiz["name"])
            ->setDifficulty($difficulty)
            ->setMaxTime($maxtime)
            ->setUser($this->getUser())
            ->setTheme($theme);
        $this->doctrine->persist($quizDoctrine);
        $this->doctrine->flush();

        foreach ($questions as $question) {
            $type = $questionTypeRepository->find($question["type"]["id"]);
            $_question = new Question();
            $_question->setStatement($question["sentence"])
                ->setPoints($question["rating"])
                ->setType($type)
                ->setQuiz($quizDoctrine);
            $this->doctrine->flush();
            $this->doctrine->persist($_question);
            foreach ($question["answers"]??[] as $answer) {
                $_answer = new PossibleAnswer();
                $_answer->setValue($answer["sentence"])
                    ->setTrue(filter_var($answer["right"],FILTER_VALIDATE_BOOLEAN))
                    ->setQuestion($_question);
                $this->doctrine->persist($_answer);
            }
        }
        $this->doctrine->flush();

        return new JsonResponse([
            'POST'=>$_POST,
        ], Response::HTTP_OK);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/quiz/send', name: 'ajax_quiz_send_answer', methods: ['POST'])]
    public function send(): Response
    {
        $QuizRepository = $this->doctrine->getRepository(Quiz::class);
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
        $this->doctrine->persist($answer);
        $this->doctrine->flush();

        return new JsonResponse($answer, Response::HTTP_OK);
    }

    #[Route('/quizs', name: 'ajax_get_all_quiz', methods: ['GET'])]
    public function getAllQuiz(): JsonResponse {
        $QuizRepository = $this->doctrine->getRepository(Quiz::class);
        $quizs = $QuizRepository->findAll();
        return new JsonResponse($quizs, Response::HTTP_OK);
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
