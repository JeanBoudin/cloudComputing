<?php

namespace App\Controller;

use App\Entity\Files;
use App\Form\FilesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FileUploader;

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

            $file = new Files();
            $formFiles = $form->getData();

            $files = $form->get('upload_path')->getData();
            $fileExtension = $files->guessExtension();

            $fileName = $form->get('filename')->getData().'.'.$fileExtension;
            $localisation = 'upload/'.md5(uniqid()).'.'.$fileExtension;

            $file->setFilename($fileName);
            $file->setUploadPath($localisation);

            $this->entityManager->persist($file);
            $this->entityManager->flush();

            $this->redirect('app_files');
        }

        return $this->render('files/index.html.twig',
            [
                'form' => $form->createView(),
            ]);
    }
}
