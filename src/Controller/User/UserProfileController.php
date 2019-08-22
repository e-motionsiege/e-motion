<?php

namespace App\Controller\User;

use App\Repository\PointRepository;
use App\Repository\UserRepository;
use App\Form\AdminUserEditType;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class UserProfileController extends AbstractController
{
    /**
     * @Route("/user/profile", name="user_profile")
     */
    public function index(Request $request, PointRepository $pointRepository)
    {
        $user = $this->getUser();
        $now = new \DateTime();
        $actual_route = $request->get('actual_route', 'user_profile');
        // get points
        $point = $pointRepository->findOneBy(['user'=>$user]);

        if (!$user) {
            $this->addFlash('danger', 'Utilisateur non trouvée');
            return $this->redirectToRoute('home');
        }

        return $this->render('user/user_profile/index.html.twig', [
            'actual_route' => $actual_route,
            'user' => $user,
            'userPoint' => $point ? $point->getValue() : 0
        ]);
    }
}
