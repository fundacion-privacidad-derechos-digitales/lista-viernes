<?php

namespace App\Entity;

use App\Encrypt\EncryptInterface;
use App\Entity\Traits\Timestampable;
use App\Hash\HashInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompareTempRepository")
 */
class CompareTemp
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    private $temp;

    public function getTemp(): ?string
    {
        return $this->temp;
    }

    public function setTemp(string $temp): self
    {
        $this->temp = strtolower($temp);

        return $this;
    }

}
