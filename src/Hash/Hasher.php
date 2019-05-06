<?php

namespace App\Hash;

class Hasher
{
    public function hash($value)
    {
        return hash('sha512', $value);
    }
}
