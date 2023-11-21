<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Security\AppAuthentificatorAuthenticator;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_registration')]
    public function index(
        Request                     $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        UserAuthenticatorInterface  $authenticator,
        AppAuthentificatorAuthenticator $login_form_authenticator,


    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $form->get('password_plain')->getData();
            $password_repeat = $form->get('password_repeat')->getData();


            if ($password_repeat !== $password) {
                $form->get('password_repeat')->addError(new FormError("Du bisch z behindert"));
            } else {
                $transaction = new Transaction();
                $transaction->setUser($user);
                $transaction->setDescription('Startguthaben');
                $transaction->setValue(500);
                $transaction->setStatus('approved');
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $password,
                    )
                );

                $entityManager->persist($user);
                $entityManager->persist($transaction);

                $entityManager->flush();


                $this->addFlash("success", "Du bsich ez offiziel Teil vom Movement!");

                return $authenticator->authenticateUser(
                    $user,
                    $login_form_authenticator,
                    $request,
                    [
                        new RememberMeBadge()
                    ]
                );

            }



        }


        return $this->render('registration/index.html.twig', [
            'form' => $form,
        ]);
    }
}
