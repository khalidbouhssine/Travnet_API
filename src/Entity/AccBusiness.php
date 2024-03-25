<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_ID', columns: ['id'])]
class AccBusiness implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 25, unique: true)]
    private ?string $proId = null;

    #[ORM\Column(type: 'string', length: 15)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 15)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 10)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 10, unique: true)]
    private ?string $cinOrPassport = null;

    #[ORM\Column(type: 'string', length: 2)]
    private ?string $role = null;

    #[ORM\Column(type: 'string', length: 15)]
    private ?string $nationality = null;

    #[ORM\Column(type: 'string', length: 64)]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private bool $valide = false;

    #[ORM\Column(type: 'boolean')]
    private bool $block = false;


    public function getId(): ?int{
        return $this->id;
    }
    public function setId(int $id): static{
        $this->id = $id;
        return $this;
    }

    public function getProId(): ?int{
        return $this->proId;
    }
    public function setProId(string $proId): static{
        $this->proId = $proId;
        return $this;
    }

    public function getFirstName(): ?string{
        return $this->firstName;
    }
    public function setFirstName(string $firstName): static{
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string{
        return $this->lastName;
    }
    public function setLastName(string $lastName): static{
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string{
        return $this->email;
    }
    public function setEmail(string $email): static{
        $this->email = $email;
        return $this;
    }

    public function getPhone(): ?string{
        return $this->phone;
    }
    public function setPhone(string $phone): static{
        $this->phone = $phone;
        return $this;
    }

    public function getCinOrPassport(): ?string{
        return $this->cinOrPassport;
    }
    public function setCinOrPassport(string $cinOrPassport): static{
        $this->cinOrPassport = $cinOrPassport;
        return $this;
    }

    public function getRole(): ?string{
        return $this->role;
    }
    public function setRole(string $role): static{
        if ($role !== null && !in_array($role, ['PH', 'AV'])) {
            throw new \InvalidArgumentException('Invalid role. Role must be either "WM" or "WX".');
        }
        $this->role = $role;
        return $this;
    }

    public function getNationality(): ?string{
        return $this->nationality;
    }
    public function setNationality(string $nationality): static{
        $this->nationality = $nationality;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): static{
        $this->password = $password;
        return $this;
    }

    public function getValide(): bool{
        return $this->valide;
    }
    public function setValide(bool $valide): static{
        $this->valide = $valide;
        return $this;
    }

    public function getBlock(): bool{
        return $this->block;
    }
    public function setBlock(bool $block): static{
        $this->block = $block;
        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // Since you've removed the roles property, you can return an empty array here
        return [];
    }
}
