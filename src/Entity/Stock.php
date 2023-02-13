<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    use DateTimeTrait;
    #[ORM\ManyToOne]
    private ?Article $article = null;

    #[ORM\ManyToOne(inversedBy: 'stocks')]
    private ?Shop $shop = null;

    #[ORM\Column]
    private ?int $quantity = 0;
    #[ORM\Column]
    private ?int $lastquantity = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getLastquantity(): ?int
    {
        return $this->lastquantity;
    }

    /**
     * @param int|null $lastquantity
     */
    public function setLastquantity(?int $lastquantity): void
    {
        $this->lastquantity = $lastquantity;
    }

}
