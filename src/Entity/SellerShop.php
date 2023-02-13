<?php

namespace App\Entity;

use App\Repository\SellerShopRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SellerShopRepository::class)]
class SellerShop
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?User $seller = null;

    #[ORM\ManyToOne]
    private ?Shop $shop = null;

    #[ORM\ManyToOne]
    private ?Caisse $caisse = null;

    #[ORM\Column]
    private ?bool $isActivate = null;

    #[ORM\Column]
    private ?float $solde = null;

    #[ORM\Column]
    private ?float $totalsell = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSeller(): ?User
    {
        return $this->seller;
    }

    public function setSeller(?User $seller): self
    {
        $this->seller = $seller;

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

    public function getCaisse(): ?Caisse
    {
        return $this->caisse;
    }

    public function setCaisse(?Caisse $caisse): self
    {
        $this->caisse = $caisse;
        return $this;
    }

    public function isIsActivate(): ?bool
    {
        return $this->isActivate;
    }

    public function setIsActivate(bool $isActivate): self
    {
        $this->isActivate = $isActivate;

        return $this;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    public function getTotalsell(): ?float
    {
        return $this->totalsell;
    }

    public function setTotalsell(float $totalsell): self
    {
        $this->totalsell = $totalsell;

        return $this;
    }
}
