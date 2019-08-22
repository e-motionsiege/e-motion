<?php

namespace App\Controller\Admin;

use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\LocationRepository;
use App\Repository\PictureVehicleRepository;
use App\Repository\VehicleRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminVehicleController extends AbstractController
{
    /**
     * @Route("/admin/vehicle", name="admin_vehicle")
     */
    public function index(Request $request, VehicleRepository $vehicleRepository, LocationRepository $locationRepository)
    {
        $actual_route = $request->get('actual_route', 'admin_vehicle');
        $now = new \DateTime();
        $allVehicles = $vehicleRepository->findAll();
        $vehiclesLocation = $locationRepository->findVehiclesLocation($now);
        $vehiclesNotAvailableIds = $locationRepository->findVehiclesLocationIds($now);
        $vehiclesAvailable = $vehicleRepository->findVehiclesAvailable($vehiclesNotAvailableIds);

        return $this->render('admin/admin_vehicle/index.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'allVehicles' => $allVehicles,
            'vehiclesLocation'=>$vehiclesLocation,
            'vehiclesAvailable'=>$vehiclesAvailable
        ]);
    }

    /**
     * @Route("/admin/vehicle/add", name="admin_vehicle_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UploadFileService $fileService)
    {
        $actual_route = $request->get('actual_route', 'admin_vehicle_add');
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);
        $now = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($vehicle->getBuyAt() > $now) {
                $this->addFlash('danger', "Votre date d'achat est supérieur à la date d'aujourd'hui");
                return $this->redirectToRoute('admin_vehicle_add');
            } else {
                if (!empty($_FILES)) {
                    $tab = $_FILES['input2']['name'];
                    for ($i = 0; $i < sizeof($tab); $i++) {
                        $fileService->uploadPictures($i, $vehicle);
                    }
                }
                $vehicle->setUser($this->getUser());
                $entityManager->persist($vehicle);
                $entityManager->flush();
                $this->addFlash('success', 'Véhicule ajouté avec succès !');
                return $this->redirectToRoute('admin_vehicle_add');
            }
        }

        return $this->render('admin/admin_vehicle/addVehicle.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/vehicle/edit/{id}", name="admin_vehicle_edit")
     */
    public function editVehicle($id, VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager, Request $request, PictureVehicleRepository $pictureVehicleRepository, UploadFileService $fileService)
    {
        $vehicle = $vehicleRepository->findOneBy(['id' => $id]);
        $actual_route = $request->get('actual_route', 'admin_location');
        $now = new \DateTime();
        if ($vehicle) {
            $pictures = $pictureVehicleRepository->findBy(['vehicle'=>$vehicle]);
            $form = $this->createForm(VehicleType::class, $vehicle);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($vehicle->getBuyAt() > $now) {
                    $this->addFlash('danger', "Votre date d'achat est supérieur à la date d'aujourd'hui");
                    return $this->redirectToRoute('admin_vehicle');
                }
                if (!empty($_FILES)) {
                    $tab = $_FILES['input2']['name'];
                    for ($i = 0; $i < sizeof($tab); $i++) {
                        $pictureVehicle = $pictureVehicleRepository->findBy(['vehicle'=>$vehicle, 'name'=>'picture'.($i+1)]);
                        if ($pictureVehicle){
                            $fileService->uploadPicturesEdit($i, $vehicle, $pictureVehicle);
                        }else{
                            $fileService->uploadPictures($i, $vehicle);
                        }
                    }
                }
                $entityManager->persist($vehicle);
                $entityManager->flush();

                $this->addFlash('success', 'Véhicule edité avec succès !');
                return $this->redirectToRoute('admin_vehicle');
            }
        } else {
            $this->addFlash('danger', 'Véhicule non trouvée');
            return $this->redirectToRoute('admin_vehicle');
        }
        return $this->render('admin/admin_vehicle/edit.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

}
