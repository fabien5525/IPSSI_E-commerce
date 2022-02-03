<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;


    #[ORM\Column(type: 'boolean')]
    private $etat;

    #[ORM\OneToOne(mappedBy: 'panier', targetEntity: User::class, cascade: ['persist', 'remove'])]
    private $user;

    #[ORM\OneToOne(inversedBy: 'panier', targetEntity: ContenuPanier::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $contenuPanier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        // set the owning side of the relation if necessary
        if ($user->getPanier() !== $this) {
            $user->setPanier($this);
        }

        $this->user = $user;

        return $this;
    }

    public function getContenuPanier(): ?ContenuPanier
    {
        return $this->contenuPanier;
    }

    public function setContenuPanier(ContenuPanier $contenuPanier): self
    {
        $this->contenuPanier = $contenuPanier;

        return $this;
    }
}
