<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MatchJobRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MatchJob
{
    use Timestampable;

    const TYPE_EMAIL = 'EMAIL';
    const TYPE_PHONE = 'PHONE';

    const STATUS_PENDING = 'PENDING';
    const STATUS_DONE = 'DONE';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PartyUser", inversedBy="matchJobs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $partyUser;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Por favor, suba el fichero encriptado.")
     */
    private $filename;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $resultFilename;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $coincidences;

    public function __construct()
    {
        $this->setFilename($this->generateUniqueFilename());
        $this->status = self::STATUS_PENDING;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPartyUser(): ?PartyUser
    {
        return $this->partyUser;
    }

    public function setPartyUser(?PartyUser $partyUser): self
    {
        $this->partyUser = $partyUser;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getResultFilename(): ?string
    {
        return $this->resultFilename;
    }

    public function setResultFilename(string $resultFilename): self
    {
        $this->resultFilename = $resultFilename;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getCoincidences(): ?int
    {
        return $this->coincidences;
    }

    public function setCoincidences(int $coincidences): self
    {
        $this->coincidences = $coincidences;

        return $this;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function generateUniqueFilename()
    {
        return md5(uniqid()) . '.csv';
    }
}
