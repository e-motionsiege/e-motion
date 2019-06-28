<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Repository\LocationRepository;
use App\Repository\OfferRepository;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminLocationEditType extends AbstractType
{
    protected $notAvailable;
    protected $vehicleRepository;
    protected $offerRepository;
    protected $userRepository;

    public function __construct(LocationRepository $locationRepository, VehicleRepository $vehicleRepository, OfferRepository $offerRepository, UserRepository $userRepository)
    {
        $this->notAvailable = $locationRepository->findVehiclesLocationIds(new \DateTime());
        $this->vehicleRepository = $vehicleRepository;
        $this->offerRepository = $offerRepository;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Début de la location'
            ])
            ->add('endAt', DateTimeType::class, [
                'required' => true,
                'widget' => 'single_text',
                'label' => 'Fin de la location'
            ])
            ->add('returnAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Retour du véhicule'
            ])
            ->add('returnKm',null, [
                'required' => false,
                'label' => 'Nombre de kilomètres effectué'
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'required' => true,
                'choices' => $this->userRepository->findAll(),
                'choice_label' => function (User $user) {
                    return $user->getFirstname() . ' ' . $user->getLastname() . ' - ' . $user->getEmail();
                },
                'label' => 'Utilisateur'

            ])
            ->add('vehicle', EntityType::class, [
                'class' => Vehicle::class,
                'required' => true,
                'choices' => $this->vehicleRepository->findAll(),
                'choice_label' => 'model',
                'label' => 'Véhicule'

            ])
            ->add('offer', EntityType::class, [
                'class' => Offer::class,
                'required' => true,
                'choices' => $this->offerRepository->findBy(['isActive' => true]),
                'choice_label' => 'name',
                'label' => 'Offre'
            ])
            ->add('submit', SubmitType::class);
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
