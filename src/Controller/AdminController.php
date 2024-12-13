<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $doctrine,
    ) {}

    #[Route('/admin', name: 'app_admin', methods: ['GET'])]
    public function createQuiz(): Response
    {
        return $this->render('admin/index.html.twig', []);
    }
}
