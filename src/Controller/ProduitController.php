<?php

namespace App\Controller;

use DateTime;
use Monolog\Logger;
use App\Entity\User;
use App\Entity\Produit;
use App\Form\ProduitType;
use Psr\Log\LoggerInterface;
use App\Entity\ProduitPanier;
use App\Form\ProduitPanierType;
use Monolog\Handler\StreamHandler;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('upload_dir'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', "Impossible d'uploader l'image");
                    return $this->redirectToRoute('home');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $produit->setPhoto($newFilename);
            }

            $entityManager->persist($produit);
            $entityManager->flush();

            $this->addFlash('success', 'salut');

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'produit_show', methods: ['GET', 'POST'])]
    public function show(Produit $produit, EntityManagerInterface $em, Request $request, TranslatorInterface $t): Response
    {
        $produitPanier = new ProduitPanier();
        /** @var \User */
        $user = $this->getUser();

        $produitPanier->setPanier($user->getPanier());
        $produitPanier->setDateAjout(new \DateTime());
        $produitPanier->setProduit($produit);

        $form = $this->createForm(ProduitPanierType::class, $produitPanier);
        $form->handleRequest($request);

        $this->addFlash("success", ($user->getPanier() != null));
        //dd($user->getPanier());
        if ($form->isSubmitted() && $form->isValid()) {
            $qt = $form->get('quantite')->getData();
            if ($qt > 0 || $qt <= $produit->getStock()) {
                //C'est bon
                $produitPanier->setQuantite($qt);
                $em->persist($produitPanier);
                $em->flush();
                //Message FLASH C BON
                $this->addFlash("success", $t->trans('ProduitPanier.success') . '(' . $produitPanier->getQuantite() . ')');
                return $this->redirectToRoute('produit_index');
            } else {
                //C'est pas bon
                $this->addFlash('danger', $t->trans('ProduitPanier.danger') . '(' . $produitPanier->getQuantite() . ')');
                return $this->redirectToRoute('produit_index');
            }
        }
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{id}/edit', name: 'produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produit/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
