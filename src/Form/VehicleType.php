<?php

namespace App\Form;

use App\Entity\Vehicle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VehicleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'placeholder' => 'Choisir',
                'choices' => [
                    'Voiture' => 'voiture',
                    'Scooter' => 'scooter',
                ],
                'required' => true,
                'label' => 'Type de véhicule'
            ])
            ->add('brand', null, [
                'required' => true,
                'label' => 'Marque'

            ])
            ->add('model', null, [
                'required' => true,
                'label' => 'Modèle'
            ])
            ->add('description', null, [
                'required' => true,
                'label' => 'Description'
            ])
            ->add('serial_number', null, [
                'required' => true,
                'label' => 'Numéro de série'
            ])
            ->add('color', null, [
                'required' => true,
                'label' => 'Couleur'
            ])
            ->add('plate_number', null, [
                'required' => true,
                'label' => "Plaque d'imatriculation"
            ])
            ->add('km', null, [
                'required' => true,
                'label' => 'Nombre de kilomètres'
            ])
            ->add('buyAt', DateType::class, [
                'widget' => 'single_text',
                'required' => true,
                'label' => "Date d'achat"

            ])
            ->add('price', null, [
                'required' => true,
                'label' => "Prix d'achat"
            ])
            //->add('user')
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicle::class,
        ]);
    }
}
