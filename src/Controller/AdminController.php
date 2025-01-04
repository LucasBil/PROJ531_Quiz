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
        private readonly EntityManagerInterface $doctrine,
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
}
