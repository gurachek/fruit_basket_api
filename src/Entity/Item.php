<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Item
 *
 * @ORM\Table(name="item")
 * @ORM\Entity
 */
class Item
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="type_id", type="integer", nullable=true)
     */
    private $typeId;

    /**
     * @var float
     *
     * @ORM\Column(name="weight", type="float", precision=10, scale=0, nullable=false)
     */
    private $weight;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $basket_id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Basket", inversedBy="items")
     * @ORM\JoinColumn(nullable=false)
     */
    private $basket;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ItemType", inversedBy="item")
     */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): self
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function toArray()
    {
        return [
            'id' => $this->getId(),
            'type_id' => $this->getTypeId(),
            'weight' => $this->getWeight(),
        ];
    }

    public function getBasketId(): ?int
    {
        return $this->basket_id;
    }

    public function setBasketId(int $basket_id): self
    {
        $this->basket_id = $basket_id;

        return $this;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function getType(): ?ItemType
    {
        return $this->type;
    }

    public function setType(ItemType $type): self
    {
        $this->type = $type;

        return $this;
    }

}
