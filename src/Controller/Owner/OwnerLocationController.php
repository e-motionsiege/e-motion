<?php

namespace App\Controller\Owner;

use App\Form\AdminLocationEditType;
use App\Form\OwnerLocationEditType;
use App\Repository\BillRepository;
use App\Repository\LocationRepository;
use App\Service\MailerService;
use App\Service\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("has_role('ROLE_USER') or has_role('ROLE_OWNER') or has_role('ROLE_PROPRIETAIRE')")
 */
class OwnerLocationController extends AbstractController
{
    /**
     * @Route("/owner/location", name="owner_location")
     */
    public function index(Request $request, LocationRepository $locationRepository)
    {
        $user = $this->getUser();
        $now = new \DateTime();
        $actual_route = $request->get('actual_route', 'owner_location');
        $vehiclesLocation = $locationRepository->findVehiclesLocationOwner($now, $user);
        $vehiclesLocationUser = $locationRepository->findVehiclesLocationOwnerUser($now, $user);

        return $this->render('owner/owner_location/index.html.twig', [
            'actual_route' => $actual_route,
            'vehiclesLocation'=>$vehiclesLocation,
            'now'=>$now,
            'vehiclesLocationUser'=>$vehiclesLocationUser
        ]);
    }

    /**
     * @Route("/owner/location/status/{id}", name="owner_location_status")
     */
    public function changeStatusLocation($id, LocationRepository $locationRepository, EntityManagerInterface $entityManager)
    {
        $location = $locationRepository->findOneBy(['id' => $id]);
        if ($location) {
            if ($location->getIsActive()) {
                $location->setIsActive(false);
                $this->addFlash('success', 'Location annulée !');
            } else {
                $location->setIsActive(true);
                $this->addFlash('success', 'Location activée !');
            }
            $entityManager->persist($location);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_location');
    }

    /**
     * @Route("/owner/location/delete/{id}", name="owner_location_delete")
     */
    public function deleteUser($id, EntityManagerInterface $entityManager, LocationRepository $locationRepository, BillRepository $billRepository)
    {
        $location = $locationRepository->findOneBy(['id' => $id]);
        if ($location) {
            $bills = $billRepository->find(['location' => $location]);
            if ($bills) {
                foreach ($bills as $bill) {
                    $bill->setLocation(null);
                    $entityManager->persist($bill);
                    $entityManager->remove($bill);
                }
                $location->setOffer(null);
                $location->setUser(null);
                $location->setVehicle(null);
                $entityManager->persist($location);
            }
        }

        $entityManager->remove($location);
        $entityManager->flush();
        $this->addFlash('success', 'Location supprimée avec succès !');

        return $this->redirectToRoute('admin_location');
    }

    /**
     * @Route("/owner/location/send/{id}", name="owner_location_send")
     */
    public function sendMail($id, MailerService $mailerService, LocationRepository $locationRepository){
        $location = $locationRepository->findOneBy(['id' => $id]);
        $mailerService->sendMailVehicleLocation($location);
        $this->addFlash('success', 'Envoie du mail avec succès !');

        return $this->redirectToRoute('admin_location');
    }

    /**
     * @Route("/owner/location/edit/{id}", name="owner_location_edit")
     */
    public function editOffer($id, LocationRepository $locationRepository, EntityManagerInterface $entityManager, Request $request, StripeService $stripeService)
    {
        $location = $locationRepository->findOneBy(['id' => $id]);
        $actual_route = $request->get('actual_route', 'owner_location');
        $return = false;

        if ($location) {
            if ($location->getReturnAt() != null){
                $return = true;
            }
            $form = $this->createForm(OwnerLocationEditType::class, $location);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($location->getStartAt()->format('Y-m-d') > $location->getEndAt()->format('Y-m-d')){
                    $this->addFlash('danger', 'La date de fin ne peut être infèrieur à la date de début');
                    return $this->redirectToRoute('owner_location');
                }
                if ($return == false){
                    if ($location->getReturnAt() != null){
                        $stripeService->authentication();
                        $stripeService->charge($location);
                    }
                }
                $entityManager->persist($location);
                $entityManager->flush();

                $this->addFlash('success', 'Location editée avec succès !');
                return $this->redirectToRoute('owner_location');
            }
        } else {
            $this->addFlash('danger', 'Location non trouvée');
            return $this->redirectToRoute('owner_location');
        }
        return $this->render('owner/owner_location/edit.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }
}
