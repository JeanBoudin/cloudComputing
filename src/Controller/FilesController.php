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
            $tokenWithExtension = md5(uniqid()).'.'.$fileExtension;
            $localisation = 'upload/'.$tokenWithExtension;

            $files->move($this->getParameter('upload_directory'), $tokenWithExtension);

            $file->setFilename($fileName);
            $file->setUploadPath($localisation);

            $this->entityManager->persist($file);
            $this->entityManager->flush();

            $this->redirect('app_files');
        }
        $listFiles = $this->entityManager->getRepository(Files::class)->findAll();

        return $this->render('files/index.html.twig',
            [
                'form' => $form->createView(),
                'listFiles' => $listFiles,
            ]);
    }

    #[Route('/download/{filePath}', name: 'download_file', requirements: ['filePath' => '.+'])]
    public function downloadFile($filePath): Response
    {
        // $filePath est déjà le chemin complet du fichier

        // Vérifie si le fichier existe
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Le fichier n\'existe pas.');
        }

        // Crée une réponse avec le fichier à télécharger
        $response = new Response(file_get_contents($filePath));

        // Définit le type MIME du fichier
        $response->headers->set('Content-Type', 'application/octet-stream');

        // Définit le nom du fichier téléchargé
        $response->headers->set('Content-Disposition', 'attachment; filename="' . basename($filePath) . '"');

        return $response;
    }

}
