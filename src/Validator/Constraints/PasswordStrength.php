<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PasswordStrength extends Constraint
{
    public $message = 'La contraseña debe tener al menos una letra mayúscula, una minúscula y un número.';
}