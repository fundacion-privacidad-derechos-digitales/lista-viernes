<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueIdNumber extends Constraint
{
    public $message = 'Este DNI/Pasaporte ya existe en la Lista Viernes. Por favor, comprueba que no estás registrado/a con otro email.';
}