<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Vehicle;
use App\Form\AdminUserEditType;
use App\Form\AdminUserType;
use App\Form\VehicleType;
use App\Repository\BillRepository;
use App\Repository\LocationRepository;
use App\Repository\PointRepository;
use App\Repository\UserRepository;
use App\Repository\VehicleRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user")
     */
    public function index(Request $request, UserRepository $userRepository)
    {
        $actual_route = $request->get('actual_route', 'admin_user');
        $users = $userRepository->findAll();
        return $this->render('admin/admin_user/index.html.twig', [
            'actual_route' => $actual_route,
            'users'=>$users,
        ]);
    }

    /**
     * @Route("/admin/user/add", name="admin_user_add")
     */
    public function add(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $actual_route = $request->get('actual_route', 'admin_user_add');
        $user = new User();
        $form = $this->createForm(AdminUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $birthdate = $user->getBirthdate();
            $age = date_diff($birthdate, date_create('today'))->y;

            if ($age<18) {
                $this->addFlash('danger', "L'utilisateur n'a pas 18 ans");
                return $this->redirectToRoute('admin_user_add');
            } else {
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur ajouté !');
                return $this->redirectToRoute('admin_user_add');
            }
        }

        return $this->render('admin/admin_user/addUser.html.twig', [
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/edit/{id}", name="admin_user_edit")
     */
    public function editOffer($id, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request)
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        $actual_route = $request->get('actual_route', 'admin_user');

        if ($user) {
            $form = $this->createForm(AdminUserEditType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $birthdate = $user->getBirthdate();
                $age = date_diff($birthdate, date_create('today'))->y;

                if ($age<18) {
                    $this->addFlash('danger', "L'utilisateur n'a pas 18 ans");
                    return $this->redirectToRoute('admin_user');
                }else{
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Utilisateur édité avec succès !');
                return $this->redirectToRoute('admin_user');
                }
            }
        } else {
            $this->addFlash('danger', 'Utilisateur non trouvée');
            return $this->redirectToRoute('admin_user');
        }
        return $this->render('admin/admin_user/edit.html.twig', [
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/user/delete/{id}", name="admin_user_delete")
     */
    public function deleteUser($id, UserRepository $userRepository, EntityManagerInterface $entityManager, LocationRepository $locationRepository, PointRepository $pointRepository, VehicleRepository $vehicleRepository, BillRepository $billRepository)
    {
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($user) {
            $locations = $locationRepository->findBy(['user' => $user]);
            if ($locations) {
                foreach ($locations as $location) {
                    $bills = $billRepository->findBy(['location'=>$location]);
                    if ($bills){
                        foreach ($bills as $bill){
                            $bill->setLocation(null);
                            $entityManager->persist($bill);
                            $entityManager->remove($bill);
                        }
                    }
                    $location->setOffer(null);
                    $location->setUser(null);
                    $location->setVehicle(null);
                    $entityManager->persist($location);
                    $entityManager->remove($location);
                }
            }
            $point = $pointRepository->findOneBy(['user'=>$user]);
            if ($point){
                $point->setUser(null);
                $entityManager->persist($point);
                $entityManager->remove($point);
            }
            $vehicles = $vehicleRepository->findBy(['user'=>$user]);
            if ($vehicles){
                foreach ($vehicles as $vehicle){
                    $vehicle->setUser(null);
                    $entityManager->persist($vehicle);
                    $entityManager->remove($vehicle);
                }
            }
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Offre supprimée avec succès !');
        }
        return $this->redirectToRoute('admin_offer');
    }
}
