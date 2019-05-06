<?php

namespace App\Form;

use App\Entity\Phone;
use App\Validator\Constraints\MaxPhones;
use App\Validator\Constraints\UniquePhone;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class PhoneFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('phone', TelType::class, [
            'label' => 'label.phone',
            'attr' => ['class' => 'phone-mask'],
            'constraints' => [
                new NotBlank([
                    'message' => 'Escribe un teléfono'
                ]),
                new Length([
                    'min' => 11,
                    'max' => 11
                ]),
                new Regex([
                    'pattern' => "/^[6789]{1}[0-9]{2} [0-9]{3} [0-9]{3}$/",
                    'message' => "No es un teléfono válido"
                ]),
                new MaxPhones([
                    'max' => 20
                ]),
                new UniquePhone()
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Phone::class,
        ]);
    }
}
