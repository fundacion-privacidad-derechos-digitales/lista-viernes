<?php

namespace App\Hash;

class HashService
{
    private $hasher;

    public function __construct(Hasher $hasher)
    {
        $this->hasher = $hasher;
    }

    public function hash(HashInterface $entity)
    {
        $hashedFields = $entity->getHashedFields();

        foreach ($hashedFields as $plainField => $hashedField) {
            $getter = 'get' . ucfirst($plainField);
            $setter = 'set' . ucfirst($hashedField);

            $entity->$setter($this->hasher->hash($entity->$getter()));
        }
    }
}
