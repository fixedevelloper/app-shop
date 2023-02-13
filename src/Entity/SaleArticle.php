<?php

namespace App\Entity;

use App\Repository\SaleArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleArticleRepository::class)]
class SaleArticle
{
    const PENDING="PENDING";
    const DELIVRED="DELIVRED";
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    use DateTimeTrait;
    #[ORM\Column(length: 255)]
    private ?string $CustomerName = null;

    #[ORM\Column]
    private ?float $amountTotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $tax = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'saleArticle', targetEntity: LineSale::class)]
    private Collection $lineArticles;

    #[ORM\ManyToOne]
    private ?SellerShop $sellerShop = null;


    public function __construct()
    {
        $this->lineArticles = new ArrayCollection();
        $this->date_created=new \DateTime('now',new \DateTimeZone('Africa/Brazzaville'));
        $this->date_modified=new \DateTime('now',new \DateTimeZone('Africa/Brazzaville'));
    }


    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCustomerName(): ?string
    {
        return $this->CustomerName;
    }

    public function setCustomerName(string $CustomerName): self
    {
        $this->CustomerName = $CustomerName;

        return $this;
    }

    public function getAmountTotal(): ?float
    {
        return $this->amountTotal;
    }

    public function setAmountTotal(float $amountTotal): self
    {
        $this->amountTotal = $amountTotal;

        return $this;
    }

    public function getTax(): ?float
    {
        return $this->tax;
    }

    public function setTax(?float $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, LineSale>
     */
    public function getLineArticles(): Collection
    {
        return $this->lineArticles;
    }

    public function addLineArticle(LineSale $lineArticle): self
    {
        if (!$this->lineArticles->contains($lineArticle)) {
            $this->lineArticles->add($lineArticle);
            $lineArticle->setSaleArticle($this);
        }

        return $this;
    }

    public function removeLineArticle(LineSale $lineArticle): self
    {
        if ($this->lineArticles->removeElement($lineArticle)) {
            // set the owning side to null (unless already changed)
            if ($lineArticle->getSaleArticle() === $this) {
                $lineArticle->setSaleArticle(null);
            }
        }

        return $this;
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
}
