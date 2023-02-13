<?php

namespace App\Entity;

use App\Repository\MouvementCaisseRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: MouvementCaisseRepository::class)]
class MouvementCaisse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;
    #[ORM\Column(length: 255)]
    private ?float $debit = 0.0;

    #[ORM\Column(length: 255)]
    private ?float $credit = 0.0;

    #[ORM\Column(length: 255)]
    private ?\DateTimeImmutable $dateoperation = null;

    #[ORM\Column(length: 255)]
    private ?string $datestring = null;
    #[ORM\ManyToOne]
    private Caisse $caisse;
    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return mixed
     */
    public function getDatestring()
    {
        return $this->datestring;
    }

    /**
     * @param mixed $datestring
     */
    public function setDatestring($datestring): void
    {
        $this->datestring = $datestring;
    }

    public function getDebit(): ?float
    {
        return $this->debit;
    }

    public function setDebit(float $debit): self
    {
        $this->debit = $debit;

        return $this;
    }

    public function getCredit(): ?float
    {
        return $this->credit;
    }

    public function setCredit(?float $credit): self
    {
        $this->credit = $credit;

        return $this;
    }

    public function getDateoperation(): ?\DateTimeInterface
    {
        return $this->dateoperation;
    }

    public function setDateoperation(\DateTimeInterface $dateoperation): self
    {
        $this->dateoperation = $dateoperation;

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
