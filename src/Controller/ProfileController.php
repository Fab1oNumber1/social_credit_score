<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Service\MediaService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Security $security,
        Request $request,
        MediaService $mediaService,
        EntityManagerInterface $entityManager,
    ): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $avatar = $form->get('avatar')->getData();
            if($avatar) {
                $media =  $mediaService->handleMediaUpload($avatar, $user, "Avatar ".$user);
                $user->setAvatar($media);
                $entityManager->persist($media);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("success", "Boah bisch du hässlich");

        }



        return $this->render('profile/index.html.twig', [
            'form' => $form,
        ]);
    }
}
