<?php

namespace App\Controller;

use App\Form\FilesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FilesController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/files', name: 'app_files')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(FilesType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();
        }

        return $this->render('files/index.html.twig',
            [
                'form' => $form->createView(),
            ]);
    }
}
