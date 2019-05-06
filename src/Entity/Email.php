<?php

namespace App\Entity;

use App\Encrypt\EncryptInterface;
use App\Entity\Traits\Timestampable;
use App\Hash\HashInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmailRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Email implements EncryptInterface, HashInterface
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    private $email;

    /**
     * @ORM\Column(type="string")
     */
    private $emailEncrypted;

    /**
     * @ORM\Column(type="string")
     */
    private $emailHash;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated = false;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $validationToken;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="emails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getEncryptedFields()
    {
        return [
            'email' => 'emailEncrypted'
        ];
    }

    public function getHashedFields()
    {
        return [
            'email' => 'emailHash'
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

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getValidationToken(): ?string
    {
        return $this->validationToken;
    }

    public function setValidationToken(string $validationToken): self
    {
        $this->validationToken = $validationToken;

        return $this;
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

    public function generateValidationToken(): self
    {
        $this->setValidationToken(md5(random_bytes(10)));

        return $this;
    }
}
