<?php

namespace App\Form;

use App\Entity\MatchJob;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatchJobFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fileData', HiddenType::class, [
                'mapped' => false
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'label.type',
                'choices' => [
                    'Email' => MatchJob::TYPE_EMAIL,
                    'Teléfono' => MatchJob::TYPE_PHONE,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MatchJob::class,
        ]);
    }
}
