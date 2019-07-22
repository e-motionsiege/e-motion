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

class OwnerLocationEditType extends AbstractType
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
            ->add('returnAt', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Retour du véhicule'
            ])
            ->add('returnKm',null, [
                'required' => false,
                'label' => 'Nombre de kilomètres effectué'
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
