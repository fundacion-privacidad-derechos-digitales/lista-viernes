<?php

namespace App\Entity;

use App\Encrypt\EncryptInterface;
use App\Entity\Traits\Timestampable;
use App\Hash\HashInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PhoneRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Phone implements EncryptInterface, HashInterface
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    private $phone;

    /**
     * @ORM\Column(type="string")
     */
    private $phoneEncrypted;

    /**
     * @ORM\Column(type="string")
     */
    private $phoneHash;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="phones")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function getEncryptedFields()
    {
        return [
            'phone' => 'phoneEncrypted'
        ];
    }

    public function getHashedFields()
    {
        return [
            'phone' => 'phoneHash'
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = preg_replace('/[^0-9]/', '', $phone);

        return $this;
    }

    public function getPhoneEncrypted(): ?string
    {
        return $this->phoneEncrypted;
    }

    public function setPhoneEncrypted($phoneEncrypted): self
    {
        $this->phoneEncrypted = $phoneEncrypted;

        return $this;
    }

    public function getPhoneHash(): ?string
    {
        return $this->phoneHash;
    }

    public function setPhoneHash(string $phoneHash): self
    {
        $this->phoneHash = $phoneHash;

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
}
