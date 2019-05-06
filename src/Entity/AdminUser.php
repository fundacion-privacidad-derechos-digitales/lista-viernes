<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Validator\Constraints as AppAssert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @UniqueEntity(fields={"email"}, message="Este email ya existe en la Lista Viernes. Puedes acceder a la aplicación desde la página de login.")
 * @ORM\Table(indexes={@ORM\Index(name="user_email_idx", columns={"email"})})
 * @ORM\HasLifecycleCallbacks
 */
class AdminUser implements UserInterface
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     *
     * @Assert\NotBlank(message="Escribe un email")
     * @Assert\Email(message="Escribe un email valido")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     *
     * @Assert\Length(min="8", minMessage="La contraseña debe tener al menos 8 caracteres", max="4096")
     * @AppAssert\PasswordStrength()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Escribe un nombre")
     * @Assert\Length(min="2", minMessage="El nombre debe tener al menos 2 caracteres", max="255")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank(message="Escribe un nombre")
     * @Assert\Length(min="2", minMessage="Los apellidos deben tener al menos 2 caracteres", max="255")
     */
    private $surname;

    public function __construct()
    {
        $this->roles[] = 'ROLE_ADMIN';
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
        $this->email = strtolower($email);

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
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
        // $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function generateRandomPassword(): string
    {
        return bin2hex(random_bytes(12));
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }
}
