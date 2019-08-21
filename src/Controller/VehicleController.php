<?php

namespace App\Controller;

use App\Entity\PictureVehicle;
use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class VehicleController extends AbstractController
{
    /**
     * @Route("/vehicle/{id}", name="show_vehicle")
     */
    public function index($id)
    {
        $em = $this->getDoctrine()->getManager();
        $vehicle = $em->getRepository(Vehicle::class)->findOneBy([
            'id' => $id
        ]);
        $owner = $vehicle->getUser();
        $vehiclePictures = $em->getRepository(PictureVehicle::class)->findBy([
            'vehicle' => $vehicle
        ]);

        return $this->render('vehicle/index.html.twig', [
            'vehicle' => $vehicle,
            'owner' => $owner,
            'vehiclePictures' => $vehiclePictures
        ]);
    }
}
