<?php

namespace App\Entity;

use App\Encrypt\EncryptInterface;
use App\Entity\Traits\Timestampable;
use App\Hash\HashInterface;
use App\Validator\Constraints as AppAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface, EncryptInterface, HashInterface
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank(message="Escribe un email")
     * @Assert\Email(message="Escribe un email valido")
     * @AppAssert\UniqueAccountEmail()
     */
    private $email;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $emailEncrypted;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    private $emailHash;

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
     * @Assert\NotBlank(message="Escribe un nombre")
     * @Assert\Length(min="2", minMessage="El nombre debe tener al menos 2 caracteres", max="255")
     */
    private $name;

    /**
     * @ORM\Column(type="string")
     */
    private $nameEncrypted;

    /**
     * @Assert\NotBlank(message="Escribe un nombre")
     * @Assert\Length(min="2", minMessage="Los apellidos deben tener al menos 2 caracteres", max="255")
     */
    private $surname;

    /**
     * @ORM\Column(type="string")
     */
    private $surnameEncrypted;

    /**
     * @Assert\NotBlank(message="Escribe tu DNI")
     * @Assert\Length(min="9", minMessage="Escribe un DNI valido", max="50")
     * @AppAssert\UniqueIdNumber()
     */
    private $idNumber;

    /**
     * @ORM\Column(type="string")
     */
    private $idNumberEncrypted;

    /**
     * @ORM\Column(type="string")
     */
    private $idNumberHash;

    /**
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank(message="Escribe tu DNI")
     * @Assert\Length(min="5", minMessage="Escribe un código postal valido", max="20")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="boolean")
     */
    private $emailValidated = false;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $emailValidationToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Email", mappedBy="user", orphanRemoval=true)
     */
    private $emails;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Phone", mappedBy="user", orphanRemoval=true)
     */
    private $phones;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
        $this->phones = new ArrayCollection();
    }

    public function getEncryptedFields()
    {
        return [
            'email' => 'emailEncrypted',
            'name' => 'nameEncrypted',
            'surname' => 'surnameEncrypted',
            'idNumber' => 'idNumberEncrypted'
        ];
    }

    public function getHashedFields()
    {
        return [
            'email' => 'emailHash',
            'idNumber' => 'idNumberHash'
        ];
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

    public function getEmailEncrypted(): ?string
    {
        return $this->emailEncrypted;
    }

    public function setEmailEncrypted($emailEncrypted): self
    {
        $this->emailEncrypted = $emailEncrypted;

        return $this;
    }

    public function getEmailHash(): ?string
    {
        return $this->emailHash;
    }

    public function setEmailHash(string $emailHash): self
    {
        $this->emailHash = $emailHash;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->email;
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function generateRandomPassword(): string
    {
        return bin2hex(random_bytes(4));
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

    public function getNameEncrypted(): ?string
    {
        return $this->nameEncrypted;
    }

    public function setNameEncrypted($nameEncrypted): self
    {
        $this->nameEncrypted = $nameEncrypted;

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

    public function getSurnameEncrypted(): ?string
    {
        return $this->surnameEncrypted;
    }

    public function setSurnameEncrypted($surnameEncrypted): self
    {
        $this->surnameEncrypted = $surnameEncrypted;

        return $this;
    }

    public function getIdNumber(): ?string
    {
        return $this->idNumber;
    }

    public function setIdNumber(string $idNumber): self
    {
        $this->idNumber = preg_replace('/[^A-Z0-9]/', '', strtoupper($idNumber));

        return $this;
    }

    public function getIdNumberEncrypted(): ?string
    {
        return $this->idNumberEncrypted;
    }

    public function setIdNumberEncrypted($idNumberEncrypted): self
    {
        $this->idNumberEncrypted = $idNumberEncrypted;

        return $this;
    }

    public function getIdNumberHash(): ?string
    {
        return $this->idNumberHash;
    }

    public function setIdNumberHash(string $idNumberHash): self
    {
        $this->idNumberHash = $idNumberHash;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getEmailValidated(): ?bool
    {
        return $this->emailValidated;
    }

    public function setEmailValidated(bool $emailValidated): self
    {
        $this->emailValidated = $emailValidated;

        return $this;
    }

    public function getEmailValidationToken(): ?string
    {
        return $this->emailValidationToken;
    }

    public function setEmailValidationToken(string $emailValidationToken): self
    {
        $this->emailValidationToken = $emailValidationToken;

        return $this;
    }

    public function generateEmailValidationToken(): self
    {
        $this->setEmailValidationToken(md5(random_bytes(10)));

        return $this;
    }

    /**
     * @return Collection|Email[]
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function addEmail(Email $email): self
    {
        if (!$this->emails->contains($email)) {
            $this->emails[] = $email;
            $email->setUser($this);
        }

        return $this;
    }

    public function removeEmail(Email $email): self
    {
        if ($this->emails->contains($email)) {
            $this->emails->removeElement($email);
            // set the owning side to null (unless already changed)
            if ($email->getUser() === $this) {
                $email->setUser(null);
            }
        }

        return $this;
    }

    public function getManagedEmail(string $email): ?Email
    {
        $managedEmail = null;

        foreach ($this->getEmails() as $emailEntity) {
            if ($emailEntity->getEmail() === $email) {
                $managedEmail = $emailEntity;
                break;
            }
        }

        return $managedEmail;
    }

    /**
     * @return Collection|Phone[]
     */
    public function getPhones(): Collection
    {
        return $this->phones;
    }

    public function addPhone(Phone $phone): self
    {
        if (!$this->phones->contains($phone)) {
            $this->phones[] = $phone;
            $phone->setUser($this);
        }

        return $this;
    }

    public function removePhone(Phone $phone): self
    {
        if ($this->phones->contains($phone)) {
            $this->phones->removeElement($phone);
            // set the owning side to null (unless already changed)
            if ($phone->getUser() === $this) {
                $phone->setUser(null);
            }
        }

        return $this;
    }
}
