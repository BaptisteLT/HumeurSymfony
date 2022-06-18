<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\HumeurTypeRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HumeurTypeRepository::class)]
class HumeurType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $image;

    #[ORM\Column(type: 'string', length: 255)]
    private $libelle;

    #[ORM\OneToMany(mappedBy: 'humeurType', targetEntity: Humeur::class)]
    private $humeur;

    #[Groups(["humeurG"])]
    #[ORM\Column(type: 'integer', unique: true)]
    private $hapinessLevel;

    public function __construct()
    {
        $this->humeur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Humeur>
     */
    public function getHumeur(): Collection
    {
        return $this->humeur;
    }

    public function addHumeur(Humeur $humeur): self
    {
        if (!$this->humeur->contains($humeur)) {
            $this->humeur[] = $humeur;
            $humeur->setHumeurType($this);
        }

        return $this;
    }

    public function removeHumeur(Humeur $humeur): self
    {
        if ($this->humeur->removeElement($humeur)) {
            // set the owning side to null (unless already changed)
            if ($humeur->getHumeurType() === $this) {
                $humeur->setHumeurType(null);
            }
        }

        return $this;
    }

    public function getHapinessLevel(): ?int
    {
        return $this->hapinessLevel;
    }

    public function setHapinessLevel(int $hapinessLevel): self
    {
        $this->hapinessLevel = $hapinessLevel;

        return $this;
    }
}
