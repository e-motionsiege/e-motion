<?php

namespace App\Controller\Owner;

use App\Entity\PictureVehicle;
use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\LocationRepository;
use App\Repository\PictureVehicleRepository;
use App\Repository\VehicleRepository;
use App\Service\UploadFileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OwnerVehicleController extends AbstractController
{
    /**
     * @Route("/owner/vehicle", name="owner_vehicle")
     */
    public function index(Request $request, VehicleRepository $vehicleRepository, LocationRepository $locationRepository)
    {
        $user = $this->getUser();
        $now = new \DateTime();
        $actual_route = $request->get('actual_route', 'owner_vehicle');
        $allVehicles = $vehicleRepository->findBy(['user'=>$user->getId()]);
        $vehiclesLocation = $locationRepository->findVehiclesLocationOwner($now, $user);
        $vehiclesNotAvailableIds = $locationRepository->findVehiclesLocationIdsOwner($now, $user);
        if (!empty($vehiclesNotAvailableIds)){
            $vehiclesAvailable = $vehicleRepository->findVehiclesAvailable($vehiclesNotAvailableIds);
        }else{
            $vehiclesAvailable = $vehicleRepository->findBy(['user'=>$user->getId()]);
        }
        return $this->render('owner/owner_vehicle/index.html.twig', [
            'actual_route' => $actual_route,
            'allVehicles'=>$allVehicles,
            'vehiclesLocation'=>$vehiclesLocation,
            'vehiclesAvailable'=>$vehiclesAvailable
        ]);
    }

    /**
 * @Route("/owner/vehicle/add", name="owner_vehicle_add")
 */
    public function add(Request $request, EntityManagerInterface $entityManager, UploadFileService $fileService)
    {
        $actual_route = $request->get('actual_route', 'owner_vehicle_add');
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);
        $now = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            if ($vehicle->getBuyAt() > $now) {
                $this->addFlash('danger', "Votre date d'achat est supérieur à la date d'aujourd'hui");
                return $this->redirectToRoute('owner_vehicle_add');
            } else {
                if (!empty($_FILES)) {
                    $tab = $_FILES['input2']['name'];
                    for ($i = 0; $i < sizeof($tab); $i++) {
                        $fileService->uploadPictures($i, $vehicle);
                    }
                }
                $vehicle->setUser($this->getUser());
                $entityManager->persist($vehicle);
                $this->getUser()->setRoles(['ROLE_PROPRIETAIRE','ROLE_USER']);
                $entityManager->persist($this->getUser());
                $entityManager->flush();
                $this->addFlash('success', 'Véhicule ajouté avec succès !');
                return $this->redirectToRoute('owner_vehicle');
            }
        }

        return $this->render('owner/owner_vehicle/add.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/owner/vehicle/edit/{id}", name="owner_vehicle_edit")
     */
    public function editVehicle($id, VehicleRepository $vehicleRepository, EntityManagerInterface $entityManager, Request $request, PictureVehicleRepository $pictureVehicleRepository, UploadFileService $fileService)
    {
        $vehicle = $vehicleRepository->findOneBy(['id' => $id]);
        $actual_route = $request->get('actual_route', 'owner_vehicle');
        $now = new \DateTime();
        if ($vehicle) {
            $form = $this->createForm(VehicleType::class, $vehicle);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($vehicle->getBuyAt() > $now) {
                    $this->addFlash('danger', "Votre date d'achat est supérieur à la date d'aujourd'hui");
                    return $this->redirectToRoute('owner_vehicle');
                }
                if (!empty($_FILES)) {
                    $tab = $_FILES['input2']['name'];
                    for ($i = 0; $i < sizeof($tab); $i++) {
                        $pictureVehicle = $pictureVehicleRepository->findOneBy(['vehicle'=>$vehicle, 'name'=>'picture'.($i+1)]);
                        if ($pictureVehicle){
                            $fileService->uploadPicturesEdit($i, $vehicle, $pictureVehicle);
                        }else{
                            $fileService->uploadPictures($i, $vehicle);
                        }
                    }
                }
                $entityManager->persist($vehicle);
                $entityManager->flush();

                $this->addFlash('success', 'Véhicule édité avec succès !');
                return $this->redirectToRoute('owner_vehicle');
            }
        } else {
            $this->addFlash('danger', 'Véhicule non trouvée');
            return $this->redirectToRoute('owner_vehicle');
        }
        return $this->render('owner/owner_vehicle/edit.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }
}
