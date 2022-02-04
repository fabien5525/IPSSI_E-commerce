<?php

namespace App\Controller;

use App\Entity\Produit;
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
}
