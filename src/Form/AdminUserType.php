<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                    'required' => true,
                    'label' => 'Pseudo'
                ]
            )
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Propriétaire' => 'ROLE_OWNER',
                    'Administrateur'=>'ROLE_ADMIN'
                ],
                'multiple'=>true,
                'required' => true,
                'label' => 'Role',
                'placeholder' => 'Choisir',
                'expanded' => true
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email'
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType:: class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Confirmé Password'),
            ))->add('firstname', null, [
                'required' => true,
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'required' => true,
                'label' => 'Nom'
            ])
            ->add('birthdate', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de naissance'
            ])
            ->add('address', null, [
                'required' => true,
                'label' => 'Adresse'
            ])
            ->add('city', null, [
                'required' => true,
                'label' => 'Ville'
            ])
            ->add('zipcode', null, [
                'required' => true,
                'label' => 'Code Postal'
            ])
            ->add('phone_number', TelType::class, [
                'required' => true,
                'label' => 'Téléphone'
            ])
            ->add('license_number', null, [
                'required' => true,
                'label' => 'Numéro de permis de conduire'
            ])
            ->add('submit', SubmitType::class);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
