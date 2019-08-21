<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Repository\PictureVehicleRepository;
use App\Repository\VehicleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Packages;
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
        $picturesVehicle = $pictureVehicleRepository->findBy(['name' => 'picture1']);
        $typeVehicle = $vehicleRepository->findAllTypeVehicle();
        $brandVehicle = $vehicleRepository->findAllBrandVehicle("");
        $modelVehicle = $vehicleRepository->findAllModelVehicle("", "");

        return $this->render('home/index.html.twig', [
            'vehicules' => $vehicules,
            'picturesVehicle' => $picturesVehicle,
            'typeVehicle' => $typeVehicle,
            'brandVehicle' => $brandVehicle,
            'modelVehicle' => $modelVehicle
        ]);
    }

    /**
     * @Route("/typeAjax", name="home_type_ajax")
     */
    public function typeAjax(Request $request, VehicleRepository $vehicleRepository)
    {
        $type = $request->get('type');
        $brand = [];
        $model = [];
        $brandVehicle = $vehicleRepository->findAllBrandVehicle($type);
        $modelVehicle = $vehicleRepository->findAllModelVehicle("", $type);

        if ($brandVehicle) {
            foreach ($brandVehicle as $oneBrand) {
                $brand[] = $oneBrand;
            }
        }

        if ($modelVehicle) {
            foreach ($modelVehicle as $oneModel) {
                $model[] = $oneModel;
            }
        }

        return $this->json([
            'brand' => $brand,
            'model' => $model
        ]);
    }

    /**
     * @Route("/brandAjax", name="home_brand_ajax")
     */
    public function brandAjax(Request $request, VehicleRepository $vehicleRepository)
    {
        $brand = $request->get('brand');
        $type = $vehicleRepository->findOneBy(['brand'=>$brand]);
        $model = [];
        $modelVehicle = $vehicleRepository->findAllModelVehicle($brand, $type->getType());

        if ($modelVehicle) {
            foreach ($modelVehicle as $oneModel) {
                $model[] = $oneModel;
            }
        }

        return $this->json([
            'model' => $model
        ]);
    }

    /**
     * @Route("/searchAjax", name="home_search_ajax")
     */
    public function searchAjax(Request $request, VehicleRepository $vehicleRepository, PictureVehicleRepository $pictureVehicleRepository)
    {
        $type = $request->get('type');
        $brand = $request->get('brand');
        $model = $request->get('model');
        $tabVehicules = [];

        $vehicules = $vehicleRepository->findSearchVehicle($type,$brand,$model);

        if ($vehicules) {
            foreach ($vehicules as $vehicule) {
                $picture = $pictureVehicleRepository->findOneBy(['name'=>'picture1', 'vehicle'=>$vehicule]);
                if ($picture){
                $tabVehicules[] = ['vehicule'=>$vehicule, 'picture'=>getenv('FRONT_BASE_PATH').'upload/picture/'.$picture->getValue(), 'path'=>$this->generateUrl('show_vehicle', array('id'=>$vehicule['id']), true )];
                }else{
                    $tabVehicules[] = ['vehicule'=>$vehicule, 'picture'=>null, 'path'=>getenv('FRONT_BASE_PATH').$this->generateUrl('show_vehicle', array('id'=>$vehicule['id']), true )];
                }
            }
        }

        return $this->json([
            'vehicules' => $tabVehicules
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
