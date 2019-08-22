<?php
/**
 * Created by PhpStorm.
 * User: likwi
 * Date: 22/08/2019
 * Time: 15:46
 */

namespace App\Form;


use App\Entity\Location;
use App\Entity\Offer;
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

class UserLocationType extends AbstractType
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

            ->add('offer', EntityType::class, [
                'class' => Offer::class,
                'required' => true,
                'placeholder'=>'Choisir',
                'choices' => $this->offerRepository->findBy(['isActive' => true]),
                'choice_label' => 'name',
                'label' => 'Offre'
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Location::class,
        ]);
    }
}
