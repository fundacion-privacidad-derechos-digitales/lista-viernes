<?php

namespace App\Validator;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RecaptchaValidator extends ConstraintValidator
{
    private $recaptchaSecretKey;

    public function __construct(ParameterBagInterface $params)
    {
        $this->recaptchaSecretKey = $params->get('recaptcha_secret_key');
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint App\Validator\Recaptcha */

        if (!$constraint instanceof Recaptcha) {
            throw new UnexpectedTypeException($constraint, Recaptcha::class);
        }

        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if (null === $value || '' === $value) {
            return;
        }

        $payload = [
            "secret" => $this->recaptchaSecretKey,
            "response" => $value
        ];

        $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $response = curl_exec($ch);

        $data = json_decode($response);

        if ($data->success === false) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
