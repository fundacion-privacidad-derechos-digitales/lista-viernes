<?php

namespace App\Encrypt;

class EncryptionService
{
    private $encryptor;

    public function __construct(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    public function encrypt(EncryptInterface $entity)
    {
        $encryptedFields = $entity->getEncryptedFields();

        foreach ($encryptedFields as $plainField => $encryptedField) {
            $getter = 'get' . ucfirst($plainField);
            $setter = 'set' . ucfirst($encryptedField);

            $entity->$setter($this->encryptor->encrypt($entity->$getter()));
        }
    }

    public function decrypt(EncryptInterface $entity)
    {
        $encryptedFields = $entity->getEncryptedFields();

        foreach ($encryptedFields as $plainField => $encryptedField) {
            $setter = 'set' . ucfirst($plainField);
            $getter = 'get' . ucfirst($encryptedField);

            if (!empty($entity->$getter())) {
                $entity->$setter($this->encryptor->decrypt($entity->$getter()));
            }
        }
    }
}
