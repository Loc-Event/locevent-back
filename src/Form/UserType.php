<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null , ['label' => 'Adresse mail'])
            // ->add('roles')
            ->add('password', null , ['label' => 'Mot de passe'])
            ->add('firstname', null , ['label' => 'Prénom'])
            ->add('lastname',  null , ['label' => 'Nom de famille'])
            ->add('isVerified', null , ['label' => 'Valider l\'utilisateur'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
