<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    #[Route('/media/{media}', name: 'app_media')]
    public function index(Media $media, string $mediaUploadDir): Response
    {
        return new BinaryFileResponse($mediaUploadDir . $media->getFilename());
    }
}
