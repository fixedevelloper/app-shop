<?php

namespace App\Entity;

use App\Repository\CaisseRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: CaisseRepository::class)]
class Caisse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(length: 255)]
    private ?float $solde = 0.0;

    #[ORM\Column(length: 255)]
    private ?bool $hasretraitespece = false;

    #[ORM\Column(length: 255)]
    private ?float $maxretraitoperation = 0.0;

    #[ORM\Column(length: 255)]
    private ?float $maxretraitperiode = 0.0;

    private ?bool $hastransfertretrait = false;

    #[ORM\ManyToOne(inversedBy: 'caisses')]
    private ?Shop $shop = null;
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSolde()
    {
        return $this->solde;
    }

    /**
     * @param mixed $solde
     */
    public function setSolde($solde): void
    {
        $this->solde = $solde;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getHasretraitespece(): ?bool
    {
        return $this->hasretraitespece;
    }

    public function setHasretraitespece(bool $hasretraitespece): self
    {
        $this->hasretraitespece = $hasretraitespece;

        return $this;
    }

    public function getMaxretraitoperation(): ?float
    {
        return $this->maxretraitoperation;
    }

    public function setMaxretraitoperation(?float $maxretraitoperation): self
    {
        $this->maxretraitoperation = $maxretraitoperation;

        return $this;
    }

    public function getMaxretraitperiode(): ?float
    {
        return $this->maxretraitperiode;
    }

    public function setMaxretraitperiode(?float $maxretraitperiode): self
    {
        $this->maxretraitperiode = $maxretraitperiode;

        return $this;
    }

    public function getHastransfertretrait(): ?bool
    {
        return $this->hastransfertretrait;
    }

    public function setHastransfertretrait(?bool $hastransfertretrait): self
    {
        $this->hastransfertretrait = $hastransfertretrait;

        return $this;
    }

    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    public function __toString()
    {
       return $this->libelle;
    }

}
