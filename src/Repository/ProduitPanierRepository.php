<?php

namespace App\Repository;

use App\Entity\ProduitPanier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProduitPanier|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProduitPanier|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProduitPanier[]    findAll()
 * @method ProduitPanier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProduitPanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProduitPanier::class);
    }
}
