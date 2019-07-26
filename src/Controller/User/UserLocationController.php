<?php

namespace App\Controller\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



class UserLocationController extends AbstractController
{
    /**
     * @Route("/user/location", name="user_location")
     */
    public function index(Request $request, LocationRepository $locationRepository)
    {
        $user = $this->getUser();
        $now = new \DateTime();
        $actual_route = $request->get('actual_route', 'user_location');
        $vehiclesLocation = $locationRepository->findVehiclesLocationOwner($now, $user);
        $vehiclesLocationUser = $locationRepository->findVehiclesLocationOwnerUser($now, $user);

        return $this->render('owner/owner_vehicle/index.html.twig', [
            'actual_route' => $actual_route,
            'vehiclesLocation'=>$vehiclesLocation,
            'now'=>$now,
            'vehiclesLocationUser'=>$vehiclesLocationUser
        ]);
    }

    /**
     * @Route("/user/location/add", name="user_location_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $actual_route = $request->get('actual_route', 'user_location_add');
        $location = new Location();
        $form = $this->createForm(LocationType::class, $location);
        $form->handleRequest($request);
        $now = new \DateTime();

        if ($form->isSubmitted() && $form->isValid()) {
            if (($location->getStartAt() < $now) || ($location->getEndtAt() < $location->getStartAt())) {
                $this->addFlash('danger', "Votre date de début doit être à partir d'aujourd'hui");
                return $this->redirectToRoute('user_location_add');
            } else {
                $location->setUser($this->getUser());
                $entityManager->persist($location);
                $this->getUser()->setRoles(['ROLE_PROPRIETAIRE','ROLE_USER']);
                $entityManager->persist($this->getUser());
                $entityManager->flush();
                $this->addFlash('success', 'Votre vehicule a été loué !');
                return $this->redirectToRoute('user_location');
            }
        }

        return $this->render('owner/user_location/add.html.twig', [
            'controller_name' => 'UserLocationController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

}
