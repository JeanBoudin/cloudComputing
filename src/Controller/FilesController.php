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
    public function index(Request $request,FileUploader $file_uploader): Response
    {

        $form = $this->createForm(FilesType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $formFiles = $form->getData();

            $file = new Files();
            foreach ($formFiles as $key => $value) {

                $file->setFilename($value);

            }

            $filename = $file_uploader->upload($file);

            dd($file,$formFiles,$filename);


            $this->entityManager->persist($formFiles);
            $this->entityManager->flush();

            $this->redirect('app_files');
        }

        return $this->render('files/index.html.twig',
            [
                'form' => $form->createView(),
            ]);
    }
}
