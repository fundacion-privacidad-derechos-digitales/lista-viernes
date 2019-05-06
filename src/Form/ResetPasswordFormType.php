<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idNumber', TextType::class, [
                'label' => 'label.idNumber',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe un DNI'
                    ]),
                    new Length([
                        'minMessage' => 'Escribe un DNI valido',
                        'min' => 9,
                        'max' => 50
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe un email'
                    ]),
                    new Email([
                        'message' => 'Escribe un email valido'
                    ])
                ]
            ]);
    }
}
