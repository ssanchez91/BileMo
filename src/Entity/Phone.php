<?php

namespace App\Entity;

use App\Repository\PhoneRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=PhoneRepository::class)
 * 
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_phones_show",
 *          parameters = { "id" = "expr(object.getId())", },
 *          absolute = true        
 *      )
 * )
 * 
 * @Hateoas\Relation(
 *      "list",
 *      href = @Hateoas\Route(
 *          "api_phones_list",
 *          absolute = true        
 *      )
 * )
 * 
 * @Serializer\ExclusionPolicy("all")
 * 
 */
class Phone
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Expose
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=100)
     * @Serializer\Expose
     */
    private $model;

    /**
     * @ORM\Column(type="float")
     * @Serializer\Expose
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     */
    private $color;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     */
    private $screenSize;

    /**
     * @ORM\Column(type="text")
     * @Serializer\Expose
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getScreenSize(): ?string
    {
        return $this->screenSize;
    }

    public function setScreenSize(string $screenSize): self
    {
        $this->screenSize = $screenSize;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
