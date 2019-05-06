<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniquePhone extends Constraint
{
    public $message = 'Lo sentimos, pero no hemos podido añadir este teléfono a la Lista Viernes.';
}