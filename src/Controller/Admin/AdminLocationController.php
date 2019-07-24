<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Form\AdminLocationEditType;
use App\Form\AdminLocationType;
use App\Repository\BillRepository;
use App\Repository\LocationRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
* @Security("has_role('ROLE_ADMIN')")
*/
class AdminLocationController extends AbstractController
{
    /**
 * @Route("/admin/location", name="admin_location")
 */
    public function index(LocationRepository $locationRepository, Request $request)
    {
        $actual_route = $request->get('actual_route', 'admin_location');
        $locations = $locationRepository->findAll();

        return $this->render('admin/admin_location/index.html.twig', [
            'actual_route' => $actual_route,
            'locations' => $locations,
            'now'=>new \DateTime()
        ]);
    }

    /**
     * @Route("/admin/location/add", name="admin_location_add")
     */
    public function add(LocationRepository $locationRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $actual_route = $request->get('actual_route', 'admin_location_add');
        $location = new Location();
        $form = $this->createForm(AdminLocationType::class, $location);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($location->getStartAt()->format('d-m-Y') > $location->getEndAt()->format('d-m-Y')){
                $this->addFlash('danger', 'La date de fin ne peut être infèrieur à la date de début');
                return $this->redirectToRoute('admin_location_add');
            }
            $entityManager->persist($location);
            $entityManager->flush();
            $this->addFlash('success', 'Location créée avec succès !');
            return $this->redirectToRoute('admin_location_add');
        }
        return $this->render('admin/admin_location/addLocation.html.twig', [
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/location/edit/{id}", name="admin_location_edit")
     */
    public function editOffer($id, LocationRepository $locationRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $location = $locationRepository->findOneBy(['id' => $id]);
        $actual_route = $request->get('actual_route', 'admin_location');

        if ($location) {
            $form = $this->createForm(AdminLocationEditType::class, $location);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if ($location->getStartAt()->format('d-m-Y') > $location->getEndAt()->format('d-m-Y')){
                    $this->addFlash('danger', 'La date de fin ne peut être infèrieur à la date de début');
                    return $this->redirectToRoute('admin_location');
                }
                $entityManager->persist($location);
                $entityManager->flush();

                $this->addFlash('success', 'Location editée avec succès !');
                return $this->redirectToRoute('admin_location');
            }
        } else {
            $this->addFlash('danger', 'Location non trouvée');
            return $this->redirectToRoute('admin_location');
        }
        return $this->render('admin/admin_location/edit.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/location/status/{id}", name="admin_location_status")
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
     * @Route("/admin/location/delete/{id}", name="admin_location_delete")
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
     * @Route("/admin/location/send/{id}", name="admin_location_send")
     */
    public function sendMail($id, MailerService $mailerService, LocationRepository $locationRepository){
        $location = $locationRepository->findOneBy(['id' => $id]);
        $mailerService->sendMailVehicleLocation($location);
        $this->addFlash('success', 'Envoie du mail avec succès !');

        return $this->redirectToRoute('admin_location');
    }
}
