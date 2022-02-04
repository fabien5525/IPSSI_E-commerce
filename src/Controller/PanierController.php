<?php

namespace App\Controller;
use App\Entity\ProduitPanier;
use App\Entity\Commande;
use App\Form\CommandeType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'panier')]
    public function index(ManagerRegistry $d, Request $r): Response
    {
        $em = $d->getManager();

        $commande = new Commande();
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($r);

        if($form->isSubmitted() && $form->isValid()){


            $this->addFlash('succes', 'Commande faite');
        }

        $paniers = $em->getRepository(ProduitPanier::class)->findAll();
        return $this->render('panier/index.html.twig', [
            'paniers' => $paniers,
            'commande' => $form->createView()
        ]);
    }
}
