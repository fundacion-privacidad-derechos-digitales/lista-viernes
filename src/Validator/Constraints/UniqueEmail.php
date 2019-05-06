<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueEmail extends Constraint
{
    public $message = 'Lo sentimos, pero no hemos podido añadir este email a la Lista Viernes.';
}