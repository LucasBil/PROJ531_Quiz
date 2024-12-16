<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Theme;


class ThemeController extends AbstractController {
    public function __construct(
        private EntityManagerInterface $doctrine,
    ) {}

    #[Route('/theme', name: 'app_theme')]
    public function index(): Response {
        $themeRepository = $this->doctrine->getRepository(Theme::class);
        $themes = $themeRepository->findAll();


        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ThemeController',
            'themes' => $themes,
        ]);
    }
}
