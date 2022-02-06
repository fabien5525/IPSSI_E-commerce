<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Entity\User;
use App\Entity\Panier;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ManagerRegistry $d, TranslatorInterface $t): Response
    {   
        $em = $d->getManager();

        $produits= $em->getRepository(Produit::class)->findAll();
        return $this->render('home/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/superAdmin', name: 'superAdmin')]
    public function super(ManagerRegistry $d, TranslatorInterface $t): Response
    {   
        $em = $d->getManager();

        $users= $em->getRepository(User::class)->findAll();
        $paniers = $em->getRepository(Panier::class)->PanierNonPaye();
        return $this->render('home/super.html.twig', [
            'users' => $users,
            'paniers' => $paniers
        ]);
    }
}
