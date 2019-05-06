<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeEmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $builder->getData();

        $builder
            ->add('oldEmail', EmailType::class, [
                'label' => 'label.oldEmail',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe un email'
                    ]),
                    new Email([
                        'message' => 'Escribe un email valido'
                    ]),
                    new EqualTo([
                        'message' => 'Este no es el email con el que estas registrado',
                        'value' => $user->getEmail()
                    ])
                ]
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Los emails no coinciden',
                'data' => '',
                'first_options' => ['label' => 'label.newEmail'],
                'second_options' => ['label' => 'label.repeatEmail'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe un email'
                    ]),
                    new Email([
                        'message' => 'Escribe un email valido'
                    ])
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
