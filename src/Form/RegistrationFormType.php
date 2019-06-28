<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', null, [
                'required' => true,
                'label' => 'Pseudo*'
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email*'
            ])
            ->add('firstname', null, [
                'required' => true,
                'label' => 'Prénom*'
            ])
            ->add('lastname', null, [
                'required' => true,
                'label' => 'Nom*'
            ])
            ->add('password', RepeatedType::class, array(
                'type' => PasswordType:: class,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit au moins contenir  {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
                'first_options' => array('label' => 'Mot de passe*'),
                'second_options' => array('label' => 'Confirmation du mot de passe*'),)
            )
            ->add('birthdate', DateType::class, [
                'required' => false,
                'label' => 'Date de naissance',
/*                'widget' => 'single_text',
                'html5' => false,
                'attr' => ['class' => 'js-datepicker']*/
            ])
            ->add('address', null, [
                'required' => true,
                'label' => 'Adresse*'
            ])
            ->add('city', null, [
                'required' => true,
                'label' => 'Ville*'
            ])
            ->add('zipcode', null, [
                'required' => true,
                'label' => 'Code Postal*'
            ])
            ->add('phone_number', TelType::class, [
                'required' => true,
                'label' => 'Numéro de téléphone*'
            ])
            ->add('license_number', null, [
                'required' => true,
                'label' => 'Numéro de license du permis de conduire*',
                'constraints' => [
                    new Length([
                        'min' => 9,
                        'max' => 9
                    ])
                ]
            ])
            ->add('submit', SubmitType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
