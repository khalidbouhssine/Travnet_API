<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_ID', columns: ['id'])]
class Validation implements UserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private ?string $email = null;
   
    #[ORM\Column(type: 'integer', unique: true)]
    private ?int $numDeValidation = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateDeSaveNumValidation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string{
        return $this->email;
    }
    public function setEmail(string $email): static{
        $this->email = $email;
        return $this;
    }

    public function getNumDeValidation(): ?int
    {
        return $this->numDeValidation;
    }

    public function setNumDeValidation(int $numDeValidation): static
    {
        $this->numDeValidation = $numDeValidation;
        return $this;
    }

    public function getDateDeSaveNumValidation(): ?\DateTimeInterface
    {
        return $this->dateDeSaveNumValidation;
    }

    public function setDateDeSaveNumValidation(?\DateTimeInterface $dateDeSaveNumValidation): static
    {
        $this->dateDeSaveNumValidation = $dateDeSaveNumValidation;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    public function eraseCredentials(): void
    {
        // Si vous stockez des données temporaires ou sensibles sur l'utilisateur, effacez-les ici
        // $this->plainPassword = null;
    }

    public function getRoles(): array
    {
        // Comme vous avez supprimé la propriété des rôles, vous pouvez retourner un tableau vide ici
        return [];
    }
}