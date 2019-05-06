<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueAccountEmail extends Constraint
{
    public $message = 'Este email ya existe en la Lista Viernes.';
}