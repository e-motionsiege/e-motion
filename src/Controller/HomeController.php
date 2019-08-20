<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\PictureVehicleRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\User;

use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, VehicleRepository $vehicleRepository, PictureVehicleRepository $pictureVehicleRepository)
    {
        $vehicules = $vehicleRepository->findAll();
        $picturesVehicle = $pictureVehicleRepository->findBy(['name'=>'picture1']);

        return $this->render('home/index.html.twig', [
            'vehicules' => $vehicules,
            'picturesVehicle'=>$picturesVehicle
        ]);
    }

    /**
     * @Route("/cgl", name="cgl")
     */
    public function cgl(Request $request)
    {
        return $this->render('cgl/index.html.twig', [

        ]);
    }

}
