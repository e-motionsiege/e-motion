<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\Offer;
use App\Entity\PictureVehicle;
use App\Entity\Vehicle;
use App\Form\UserLocationTypeVoiture;
use App\Form\UserLocationTypeScooter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    /**
     * @Route("/vehicle/{id}", name="show_vehicle")
     */
    public function index($id, Request $request, EntityManagerInterface $entityManager)
    {
        $em = $this->getDoctrine()->getManager();
        $vehicle = $em->getRepository(Vehicle::class)->findOneBy([
            'id' => $id
        ]);
        $owner = $vehicle->getUser();
        $vehiclePictures = $em->getRepository(PictureVehicle::class)->findBy([
            'vehicle' => $vehicle
        ]);

        $location = new Location();
        if ($vehicle->getType() == 'voiture') {
            $form = $this->createForm(UserLocationTypeVoiture::class, $location);
        } elseif ($vehicle->getType() == 'scooter') {
            $form = $this->createForm(UserLocationTypeScooter::class, $location);
        }
        $form->handleRequest($request);
        $now = new \DateTime();

        $offer = $em->getRepository(Offer::class)->findBy([
            'type' => $vehicle->getType(),
            'isActive' => true
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $startAt = $location->getStartAt()->format('Y-m-d');
            $endAt = $location->getEndAt()->format('Y-m-d');
            $start = strtotime($startAt);
            $end = strtotime($endAt);
            $diffStartEnd = $end - $start;
            $totalDay = (int)round($diffStartEnd / (60 * 60 * 24));

            if ($totalDay > $location->getOffer()->getDuration()) {
                $this->addFlash('danger', "La durée maximale de cette offre est de " . $location->getOffer()->getDuration() . " jours");
                return $this->redirectToRoute('show_vehicle', array('id' => $id));
            }

            $vehicleInLocation = $em->getRepository(Location::class)->findBy([
                'vehicle' => $vehicle
            ]);
            foreach ($vehicleInLocation as $userVehicle) {
                if (($location->getStartAt() >= $userVehicle->getStartAt()) || ($location->getStartAt() <= $userVehicle->getEndAt())) {
                    if (($location->getEndAt() < $userVehicle->getStartAt()) || ($location->getEndAt() > $userVehicle->getEndAt())) {
                        $location->setUser($this->getUser());
                        $location->setVehicle($vehicle);
                        $entityManager->persist($location);
                        $this->getUser()->setRoles(['ROLE_PROPRIETAIRE', 'ROLE_USER']);
                        $entityManager->persist($this->getUser());
                        $entityManager->flush();
                        $this->addFlash('success', 'Votre vehicule a été loué !');
                        return $this->redirectToRoute('show_vehicle', array('id' => $id));
                    } else {
                        $this->addFlash('danger', "Ce vehicule n'est pas disponible du " . $userVehicle->getStartAt() . "au" . $userVehicle->getEndAt());
                        return $this->redirectToRoute('show_vehicle', array('id' => $id));

                    }
                } else {
                    $this->addFlash('danger', "Ce vehicule n'est pas disponible du " . $userVehicle->getStartAt() . "au" . $userVehicle->getEndAt());
                    return $this->redirectToRoute('show_vehicle', array('id' => $id));

                }
            }
        }

        return $this->render('vehicle/index.html.twig', [
            'vehicle' => $vehicle,
            'owner' => $owner,
            'vehiclePictures' => $vehiclePictures,
            'form' => $form->createView(),
            'offers' => $offer
        ]);
    }

}
