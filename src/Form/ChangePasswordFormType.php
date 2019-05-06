<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\PasswordStrength;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'label' => 'label.oldPassword',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe una contraseña'
                    ]),
                    new UserPassword([
                        'message' => 'Esta no es tu contraseña actual'
                    ])
                ]
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden',
                'required' => true,
                'mapped' => false,
                'first_options' => ['label' => 'label.newPassword'],
                'second_options' => ['label' => 'label.repeatPassword'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe una contraseña'
                    ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'La contraseña debe tener al menos 8 caracteres',
                        'max' => 4096, // max length allowed by Symfony for security reasons
                    ]),
                    new PasswordStrength()
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
