<?php

namespace App\Entity;

use App\Repository\ProduitPanierRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitPanierRepository::class)]
class ProduitPanier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

     /**
     * @Assert\NotNull
     */
    #[ORM\Column(type: 'datetime')]
    private $dateAjout;

     /**
     * @Assert\NotNull
     */
    #[ORM\Column(type: 'integer')]
    private $quantite;

     /**
     * @Assert\NotNull
     */
    #[ORM\OneToOne(targetEntity: Produit::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $produit;

     /**
     * @Assert\NotNull
     */
    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'produitPaniers')]
    #[ORM\JoinColumn(nullable: false)]
    private $panier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateAjout(): ?\DateTimeInterface
    {
        return $this->dateAjout;
    }

    public function setDateAjout(\DateTimeInterface $dateAjout): self
    {
        $this->dateAjout = $dateAjout;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }
}
