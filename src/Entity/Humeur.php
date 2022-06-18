<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\HumeurRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: HumeurRepository::class)]
class Humeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["humeurG"])]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'humeurs')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[Groups(["humeurG"])]
    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[Groups(["humeurG"])]
    #[ORM\ManyToOne(targetEntity: HumeurType::class, inversedBy: 'humeur')]
    #[ORM\JoinColumn(nullable: false)]
    private $humeurType;

    #[Groups(["humeurG"])]
    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getHumeurType(): ?HumeurType
    {
        return $this->humeurType;
    }

    public function setHumeurType(?HumeurType $humeurType): self
    {
        $this->humeurType = $humeurType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}
