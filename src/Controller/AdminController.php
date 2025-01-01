<?php

namespace App\Controller;

use App\Entity\PossibleAnswer;
use App\Entity\Question;
use App\Entity\QuestionType;
use App\Entity\Quiz;
use App\Entity\Theme;
use App\Enum\Difficulty;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use function Symfony\Component\Translation\t;

#[Route('/admin')]
#[IsGranted("ROLE_ADMIN")]
class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $doctrine,
    ) {}

    #[Route('/', name: 'app_admin', methods: ['GET'])]
    public function formQuiz(): Response
    {
        $questionTypeRepository = $this->doctrine->getRepository(QuestionType::class);
        $themeRepository = $this->doctrine->getRepository(Theme::class);

        $questionTypes = $questionTypeRepository->findAll();
        $themes = $themeRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'questionTypes' => $questionTypes,
            'themes' => $themes,
        ]);
    }

    #[Route('/ajax/quiz/create', name: 'ajax_quiz_create', methods: ['POST'])]
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
            ->setDifficulty($difficulty->value)
            ->setMaxTime($maxtime)
            ->setIdUser($this->getUser())
            ->setIdTheme($theme);
        $this->doctrine->persist($quizDoctrine);
        $this->doctrine->flush();

        foreach ($questions as $question) {
            $type = $questionTypeRepository->find($question["type"]["id"]);
            $_question = new Question();
            $_question->setStatement($question["sentence"])
                ->setPoints($question["rating"])
                ->setIdType($type)
                ->setIdQuiz($quizDoctrine);
            $this->doctrine->flush();
            $this->doctrine->persist($_question);
            foreach ($question["answers"]??[] as $answer) {
                $_answer = new PossibleAnswer();
                $_answer->setValue($answer["sentence"])
                        ->setTrue($answer["right"])
                        ->setIdQuestion($_question);
                $this->doctrine->persist($_answer);
            }
        }
        $this->doctrine->flush();

        return new JsonResponse([
            'POST'=>$_POST,
        ], Response::HTTP_OK);
    }
}
