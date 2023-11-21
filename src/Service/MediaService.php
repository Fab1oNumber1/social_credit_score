<?php

namespace App\Service;


use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class MediaService
{
    public function __construct(private string $mediaUploadDir, private SluggerInterface $slugger)
    {
    }

    public function handleMediaUpload($upload, User $author, string $name = ''): Media
    {
        $media = new Media();
        $media->setAuthor($author);
        $media->setName($name);
        $originalFilename = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $upload->guessExtension();

        @mkdir($this->mediaUploadDir);
        // Move the file to the directory where brochures are stored
        $upload->move(
            $this->mediaUploadDir,
            $newFilename
        );

        $media->setFilename($newFilename);
        return $media;

    }

}