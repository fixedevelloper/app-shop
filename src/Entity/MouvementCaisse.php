<?php

namespace App\Entity;

use App\Repository\MouvementCaisseRepository;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: MouvementCaisseRepository::class)]
class MouvementCaisse
{
    const PENDING="PENDING";
    const ACCEPTED="ACCEPTED";
    const REFUSED="REFUSED";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;
    #[ORM\Column(length: 255)]
    private ?string $description = null;
    #[ORM\Column(length: 255)]
    private ?float $debit = 0.0;

    #[ORM\Column(length: 255)]
    private ?float $credit = 0.0;

    #[ORM\Column(length: 255)]
    private ?\DateTimeImmutable $dateoperation = null;

    #[ORM\Column(length: 255)]
    private ?string $datestring = null;
    #[ORM\Column(length: 255)]
    private ?string $status = null;
    #[ORM\ManyToOne]
    private Caisse $caisse;
    #[ORM\ManyToOne]
    private ?User $benficiary = null;
    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }


    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getBenficiary(): ?User
    {
        return $this->benficiary;
    }

    public function setBenficiary(?User $benficiary): self
    {
        $this->benficiary = $benficiary;
        return $this;
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
