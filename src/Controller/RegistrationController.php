<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Panier;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use App\Security\AuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;



class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AuthAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $panier = new Panier();
            $panier->setEtat(false);
            $panier->setUser($user);
            $user->setPanier($panier);

            $entityManager->persist($panier);
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register/{id}', name: 'modif_profil')]
    public function show(User $user = null, Request $r, ManagerRegistry $d, UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $t)
    {
        if ($user == null) {
            $this->addFlash('danger', $t->trans('Utilisateur.inconnue'));
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($r);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $d->getManager();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', $t->trans('Utilisateur.maj'));
        }

        return $this->render('registration/profil.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
