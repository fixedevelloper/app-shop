<?php

namespace App\Entity;

use App\Repository\JourneeComptableRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: JourneeComptableRepository::class)]
class JourneeComptable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?float $soldeouverture = 0.0;

    #[ORM\Column(length: 255)]
    private ?\DateTimeImmutable $datecomptable = null;

    #[ORM\Column(length: 255)]
    private ?float $versement = 0.0;

    #[ORM\Column(length: 255)]
    private ?float $retrait = 0.0;
    #[ORM\Column(length: 255)]
    private ?float $soldetheorique = 0.0;

    #[ORM\Column(length: 255)]
    private ?bool $status = false;
    #[ORM\ManyToOne]
    private Caisse $caisse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldeouverture(): ?float
    {
        return $this->soldeouverture;
    }

    public function setSoldeouverture(float $soldeouverture): self
    {
        $this->soldeouverture = $soldeouverture;

        return $this;
    }

    public function getDatecomptable(): ?\DateTimeImmutable
    {
        return $this->datecomptable;
    }

    public function setDatecomptable(\DateTimeImmutable $datecomptable): self
    {
        $this->datecomptable = $datecomptable;

        return $this;
    }

    public function getVersement(): ?float
    {
        return $this->versement;
    }

    public function setVersement(float $versement): self
    {
        $this->versement = $versement;

        return $this;
    }

    public function getRetrait(): ?float
    {
        return $this->retrait;
    }

    public function setRetrait(float $retrait): self
    {
        $this->retrait = $retrait;

        return $this;
    }

    public function getSoldetheorique(): ?float
    {
        return $this->soldetheorique;
    }

    public function setSoldetheorique(float $soldetheorique): self
    {
        $this->soldetheorique = $soldetheorique;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

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
