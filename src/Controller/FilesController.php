<?php

namespace App\Controller;

use App\Entity\Files;
use App\Form\FilesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;

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

            $fileName = $form->get('filename')->getData() . '.' . $fileExtension;
            $tokenWithExtension = md5(uniqid()) . '.' . $fileExtension;

            // Enregistrer le fichier temporairement
            $tempDirectory = $this->getParameter('upload_directory');
            $files->move($tempDirectory, $tokenWithExtension);
            $tempFilepath = $tempDirectory . '/' . $tokenWithExtension;

            // Azure Blob Storage
            $connectionString = "DefaultEndpointsProtocol=https;AccountName=sarahstockage123456789;AccountKey=y4Eynf0q3RXz4f6ZVA+TSrIlL89TV0LKW91SZTNdG3JDfhxHLEoHF3QWO0bbCibx6aAXJqv6w0g1+AStrnWQbw==;EndpointSuffix=core.windows.net";
            $blobClient = BlobRestProxy::createBlobService($connectionString);
            $content = fopen($tempFilepath, "r");
            $blobClient->createBlockBlob("bloup", $fileName, $content);

            // Supprimer le fichier temporaire
            unlink($tempFilepath);

            $file->setFilename($fileName);
            // Le chemin du fichier est maintenant dans Azure Blob Storage
            $file->setUploadPath('https://sarahstockage123456789.blob.core.windows.net/your-container/' . $fileName);

            $this->entityManager->persist($file);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_files');
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
