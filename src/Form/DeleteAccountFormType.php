<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeleteAccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', PasswordType::class, [
                'required' => true,
                'label' => 'label.password',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe una contraseña'
                    ]),
                    new UserPassword([
                        'message' => 'Esta no es tu contraseña'
                    ])
                ],
            ]);
    }
}
