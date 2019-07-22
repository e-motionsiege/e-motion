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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                    'required' => true,
                    'label' => 'Pseudo'
                ]
            )
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email'
            ])
            ->add('firstname', null, [
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
            ->add('phone_number', null, [
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
