<?php
namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\MissingOptionsException;

/**
 * @Annotation
 */
class MaxPhones extends Constraint
{
    public $message = 'Has alcanzado el límite de teléfonos que puedes añadir a la Lista Viernes.';

    public $max;

    public function __construct($options = null)
    {
        if (null !== $options && !\is_array($options)) {
            $options = [
                'max' => $options,
            ];
        }

        parent::__construct($options);

        if (null === $this->max) {
            throw new MissingOptionsException(sprintf('Option "max" must be given for constraint %s', __CLASS__), ['max']);
        }
    }
}