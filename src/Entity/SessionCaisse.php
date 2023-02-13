<?php

namespace App\Entity;

use App\Repository\SessionCaisseRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: SessionCaisseRepository::class)]
class SessionCaisse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $codecaisse = null;

    #[ORM\Column(length: 255)]
    private ?float $totalcaisse = 0.0;

    #[ORM\Column(length: 255)]
    private ?float $restecaisse = 0.0;

    #[ORM\Column(length: 255)]
    private ?bool $active = false;

    #[ORM\Column(length: 255)]
    private ?\DateTimeImmutable $dateStart = null;

    #[ORM\Column(length: 255)]
    private ?\DateTimeImmutable $dateEnd = null;

    #[ORM\ManyToOne]
    private ?SellerShop $sellerShop = null;

    #[ORM\ManyToOne]
    private ?Caisse $caisse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodecaisse(): ?string
    {
        return $this->codecaisse;
    }

    public function setCodecaisse(string $codecaisse): self
    {
        $this->codecaisse = $codecaisse;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalcaisse()
    {
        return $this->totalcaisse;
    }

    /**
     * @param mixed $totalcaisse
     */
    public function setTotalcaisse($totalcaisse): void
    {
        $this->totalcaisse = $totalcaisse;
    }

    /**
     * @return mixed
     */
    public function getRestecaisse()
    {
        return $this->restecaisse;
    }

    /**
     * @param mixed $restecaisse
     */
    public function setRestecaisse($restecaisse): void
    {
        $this->restecaisse = $restecaisse;
    }

    /**
     * @return mixed
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param mixed $dateStart
     */
    public function setDateStart($dateStart): void
    {
        $this->dateStart = $dateStart;
    }

    /**
     * @return mixed
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * @param mixed $dateEnd
     */
    public function setDateEnd($dateEnd): void
    {
        $this->dateEnd = $dateEnd;
    }

    public function getSellerShop(): ?SellerShop
    {
        return $this->sellerShop;
    }

    public function setSellerShop(?SellerShop $sellerShop): self
    {
        $this->sellerShop = $sellerShop;

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
}
