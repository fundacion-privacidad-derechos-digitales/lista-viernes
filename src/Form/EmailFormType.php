<?php

namespace App\Form;

use App\Validator\Constraints\MaxEmails;
use App\Validator\Constraints\UniqueEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class EmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => 'Los emails no coinciden',
                'data' => '',
                'first_options' => ['label' => 'label.email'],
                'second_options' => ['label' => 'label.repeatEmail'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe un email'
                    ]),
                    new Email([
                        'message' => 'Escribe un email valido'
                    ]),
                    new UniqueEmail(),
                    new MaxEmails([
                        'max' => 20
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => \App\Entity\Email::class,
        ]);
    }
}
