<?php

namespace App\Controller;

use App\Entity\Location;
use App\Entity\PictureVehicle;
use App\Entity\Vehicle;
use App\Form\UserLocationType;
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
        $form = $this->createForm(UserLocationType::class, $location);
        $form->handleRequest($request);
        $now = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            if (($location->getStartAt() < $now) || ($location->getEndtAt() < $location->getStartAt())) {
                $this->addFlash('danger', "Votre date de début doit être à partir d'aujourd'hui");
                return $this->redirectToRoute('user_location_add');
            } else {
                $location->setUser($this->getUser());
                $location->setVehicle($vehicle);
                $entityManager->persist($location);
                $this->getUser()->setRoles(['ROLE_PROPRIETAIRE','ROLE_USER']);
                $entityManager->persist($this->getUser());
                $entityManager->flush();
                $this->addFlash('success', 'Votre vehicule a été loué !');
                return $this->redirectToRoute('user_location');
            }
        }

        return $this->render('vehicle/index.html.twig', [
            'vehicle' => $vehicle,
            'owner' => $owner,
            'vehiclePictures' => $vehiclePictures,
            'form' => $form->createView()
        ]);
    }

}
