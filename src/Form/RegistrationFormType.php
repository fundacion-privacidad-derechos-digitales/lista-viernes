<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\PasswordStrength;
use App\Validator\Recaptcha;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('surname', TextType::class, ['label' => 'label.surname'])
            ->add('idNumber', TextType::class, ['label' => 'label.idNumber'])
            ->add('postalCode', TextType::class, ['label' => 'label.postalCode'])
            ->add('email', EmailType::class, ['label' => 'label.email'])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'mapped' => false,
                'invalid_message' => 'Las contraseñas no coinciden',
                'first_options' => ['label' => 'label.password'],
                'second_options' => ['label' => 'label.repeatPassword'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Escribe una contraseña'
                    ]),
                    new Length([
                        'minMessage' => 'La contraseña debe tener al menos 8 caracteres',
                        'min' => 8,
                        'max' => 4096, // max length allowed by Symfony for security reasons
                    ]),
                    new PasswordStrength()
                ],
            ])
            ->add('termsAccepted', CheckboxType::class, [
                'mapped' => false,
                'constraints' => new IsTrue([
                    'message' => 'Debes aceptar los términos y condiciones'
                ])
            ])
            ->add('recaptchaToken', HiddenType::class, [
                'mapped' => false,
                'data' => '',
                'constraints' => [
                    new NotBlank(['message' => 'No hemos podido comprobar si eres un robot, por favor, inténtalo de nuevo']),
                    new Recaptcha(['message' => 'No hemos podido comprobar si eres un robot, por favor, inténtalo de nuevo'])
                ],
                'error_bubbling' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
