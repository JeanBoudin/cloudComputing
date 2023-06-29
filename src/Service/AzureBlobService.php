<?php
declare(strict_types=1);

namespace App\Service;

use MicrosoftAzure\Storage\Blob\BlobRestProxy;

class AzureBlobService
{
    private BlobRestProxy $blobClient;

    public function __construct(BlobRestProxy $blobClient)
    {
        $this->blobClient = $blobClient;
    }
}