<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Entity\ProduitPanier;
use App\Form\FakeCommandeType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(ManagerRegistry $d, Request $r, EntityManagerInterface $em): Response
    {
        $em = $d->getManager();

        $commande = new Commande();
        $commande->setDateAchat(new \DateTime());
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($r);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var \User */
            $user = $this->getUser();
            $panier = $user->getPanier();
            $panier->setEtat(true);
            $em->persist($panier);
            $em->flush();

            $this->addFlash('succes', 'Commande en cours');
            return $this->redirectToRoute('commandePanier');
        }

        $paniers = $em->getRepository(ProduitPanier::class)->findAll();
        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'commande' => $form->createView()
        ]);
    }
    #[Route('/panier/commander', name: 'commandePanier')]
    public function commander(ManagerRegistry $d, Request $r, EntityManagerInterface $em): Response
    {
        $em = $d->getManager();
        $form = $this->createForm(FakeCommandeType::class);
        $form->handleRequest($r);

        if ($form->isSubmitted()) {

            /** @var User */
            $user = $this->getUser();
            /**
             * @var Panier
             */
            $panier = $user->getPanier();
            if ($panier->getEtat() == true) {
                $commande = new commande();
                $commande->setDateAchat(new \DateTime());
                $somme = 0;
                foreach ($panier->getProduitPaniers() as $produitp) {
                    $somme += $produitp->getProduit()->getPrix();
                    $commande->addProduitPanier($produitp);
                }
                $commande->setMontantTotal($somme);
                $em->persist($commande);

                $nouveauPanier = new Panier();
                $nouveauPanier->setEtat(false);
                $em->persist($nouveauPanier);
                $user->setPanier($nouveauPanier);

                //ne marche pas ?????? (Pas de nouveau panier ???)
                $em->persist($user);
                $em->flush();

                $this->addFlash('succes', 'Commande Payé !');
                return $this->redirectToRoute('panier');
            } else {
                $this->addFlash('danger', 'Panier non valide.');
            }
        }

        $paniers = $em->getRepository(ProduitPanier::class)->findAll();
        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'commande' => $form->createView()
        ]);
    }
}
