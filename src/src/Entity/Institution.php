<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\InstitutionRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Point;

/**
 * @ApiResource(
 *      collectionOperations={
 *          "get"={
 *               "filters"={
 *                      "institution.distance_filter",
 *                }
 *           }
 *      },
 *      itemOperations={"get"},
 * )
 * @ORM\Entity(repositoryClass=InstitutionRepository::class)
 * @ORM\Table(name="institution", indexes={
 *    @ORM\Index(name="institution_location_idx", columns={"location"}, flags={"spatial"})})
 * })
 */
class Institution
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="point")
     *
     * @var Point
     *
     */
    private $location;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
     * @param Point $location
     */
    public function setLocation(Point $location)
    {
        $this->location = $location;
    }

    /**
     * @return Point
     */
    public function getLocation()
    {
        return $this->location;
    }
}
