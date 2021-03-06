<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Humeur::class, orphanRemoval: true)]
    private $humeurs;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: MyDayPost::class, orphanRemoval: true)]
    private $myDayPosts;

    public function __construct()
    {
        $this->humeurs = new ArrayCollection();
        $this->myDayPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Humeur>
     */
    public function getHumeurs(): Collection
    {
        return $this->humeurs;
    }

    public function addHumeur(Humeur $humeur): self
    {
        if (!$this->humeurs->contains($humeur)) {
            $this->humeurs[] = $humeur;
            $humeur->setUser($this);
        }

        return $this;
    }

    public function removeHumeur(Humeur $humeur): self
    {
        if ($this->humeurs->removeElement($humeur)) {
            // set the owning side to null (unless already changed)
            if ($humeur->getUser() === $this) {
                $humeur->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MyDayPost>
     */
    public function getMyDayPosts(): Collection
    {
        return $this->myDayPosts;
    }

    public function addMyDayPost(MyDayPost $myDayPost): self
    {
        if (!$this->myDayPosts->contains($myDayPost)) {
            $this->myDayPosts[] = $myDayPost;
            $myDayPost->setUser($this);
        }

        return $this;
    }

    public function removeMyDayPost(MyDayPost $myDayPost): self
    {
        if ($this->myDayPosts->removeElement($myDayPost)) {
            // set the owning side to null (unless already changed)
            if ($myDayPost->getUser() === $this) {
                $myDayPost->setUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->email;
    }
}
