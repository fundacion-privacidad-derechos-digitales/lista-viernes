<?php

namespace App\Form;

use App\Entity\PartyUser;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartyUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'label.name'])
            ->add('surname', TextType::class, ['label' => 'label.surname'])
            ->add('email', EmailType::class, ['label' => 'label.email'])
            ->add('politicalParty', TextType::class, ['label' => 'label.politicalParty']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PartyUser::class,
        ]);
    }
}
