<?php

namespace App\Encrypt;

use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto;
use ParagonIE\HiddenString\HiddenString;
use Symfony\Component\HttpKernel\KernelInterface;

class Encryptor
{
    private $encryptionKey;

    public function __construct(KernelInterface $kernel)
    {
        $this->encryptionKey = KeyFactory::loadEncryptionKey($kernel->getProjectDir() . '/.key');
    }

    public function encrypt($value)
    {
        return Crypto::encrypt(new HiddenString($value), $this->encryptionKey);
    }

    public function decrypt($value)
    {
        return Crypto::decrypt($value, $this->encryptionKey);
    }
}
