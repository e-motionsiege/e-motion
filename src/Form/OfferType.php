<?php

namespace App\Form;

use App\Entity\Offer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('km', null, [
                'required' => true,
                'label' => 'Nombre de kilomètres maximum'

            ])
            ->add('duration', null, [
                'required' => true,
                'label' => 'Durée maximum en jours'

            ])
            ->add('amountKm', null, [
                'required' => true,
                'label' => 'Montant par kilomètre'

            ])
            ->add('amountDuration', null, [
                'required' => true,
                'label' => 'Montant par jour'

            ])
            ->add('isActive', ChoiceType::class, [
                'required' => true,
                'label' => 'Offre active ?',
                'placeholder' => 'Choisir',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],

            ])
            ->add('type', ChoiceType::class, [
                'required' => true,
                'label' => 'Type de véhicules concernés ?',
                'placeholder' => 'Choisir',
                'choices' => [
                    'Voiture' => 'voiture',
                    'Scooter' => 'scooter',
                ],
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
