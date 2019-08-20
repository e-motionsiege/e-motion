<?php

namespace App\Controller\Admin;

use App\Entity\Offer;
use App\Entity\Vehicle;
use App\Form\OfferType;
use App\Form\VehicleType;
use App\Repository\LocationRepository;
use App\Repository\OfferRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminOfferController extends AbstractController
{
    /**
     * @Route("/admin/offer", name="admin_offer")
     */
    public function index(Request $request, OfferRepository $offerRepository)
    {
        $actual_route = $request->get('actual_route', 'admin_offer');
        $activeOffers = $offerRepository->findBy(['isActive' => true]);
        $inactiveOffers = $offerRepository->findBy(['isActive' => false]);

        return $this->render('admin/admin_offer/index.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'activeOffers' => $activeOffers,
            'inactiveOffers' => $inactiveOffers,
        ]);
    }

    /**
     * @Route("/admin/offer/add", name="admin_offer_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager)
    {
        $actual_route = $request->get('actual_route', 'admin_offer_add');
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($offer);
            $entityManager->flush();
            $this->addFlash('success', 'Offre créée avec succès !');
            return $this->redirectToRoute('admin_offer_add');
        }


        return $this->render('admin/admin_offer/addOffer.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/offer/status/{id}", name="admin_offer_status")
     */
    public function changeStatusOffer($id, OfferRepository $offerRepository, EntityManagerInterface $entityManager)
    {
        $offer = $offerRepository->findOneBy(['id' => $id]);
        if ($offer) {
            if ($offer->getIsActive()) {
                $offer->setIsActive(false);
                $this->addFlash('success', 'Offre désactivée !');
            } else {
                $offer->setIsActive(true);
                $this->addFlash('success', 'Offre activée !');
            }
            $entityManager->persist($offer);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_offer');
    }

    /**
     * @Route("/admin/offer/edit/{id}", name="admin_offer_edit")
     */
    public function editOffer($id, OfferRepository $offerRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $offer = $offerRepository->findOneBy(['id' => $id]);
        $actual_route = $request->get('actual_route', 'admin_offer');

        if ($offer) {
            $form = $this->createForm(OfferType::class, $offer);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($offer);
                $entityManager->flush();

                $this->addFlash('success', 'Offre éditée avec succès !');
                return $this->redirectToRoute('admin_offer');
            }
        } else {
            $this->addFlash('danger', 'Offre non trouvée');
            return $this->redirectToRoute('admin_offer');
        }
        return $this->render('admin/admin_offer/edit.html.twig', [
            'controller_name' => 'AdminVehicleController',
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/offer/delete/{id}", name="admin_offer_delete")
     */
    public function deleteOffer($id, OfferRepository $offerRepository, EntityManagerInterface $entityManager, LocationRepository $locationRepository)
    {
        $offer = $offerRepository->findOneBy(['id' => $id]);
        if ($offer) {
            $locations = $locationRepository->findBy(['offer' => $offer]);
            if ($locations) {
                foreach ($locations as $location) {
                    $location->setOffer(null);
                    $entityManager->persist($location);
                }
            }
            $entityManager->remove($offer);
            $entityManager->flush();
            $this->addFlash('success', 'Offre supprimée avec succès !');
        }
        return $this->redirectToRoute('admin_offer');
    }
}
