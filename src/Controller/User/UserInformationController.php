<?php

namespace App\Controller\User;

use App\Form\AdminUserEditType;
use App\Form\UserEditType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("has_role('ROLE_USER') or has_role('ROLE_OWNER') or has_role('ROLE_PROPRIETAIRE')")
 */
class UserInformationController extends AbstractController
{
    /**
     * @Route("/user/information", name="user_information")
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $now = new \DateTime();
        $actual_route = $request->get('actual_route', 'user_information');

        if ($user) {
            $form = $this->createForm(UserEditType::class, $user);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $birthdate = $user->getBirthdate();
                $age = date_diff($birthdate, date_create('today'))->y;

                if ($age<18) {
                    $this->addFlash('danger', "L'utilisateur n'a pas 18 ans");
                    return $this->redirectToRoute('user_information');
                }else{
                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash('success', 'Utilisateur édité avec succès !');
                    return $this->redirectToRoute('user_information');
                }
            }
        } else {
            $this->addFlash('danger', 'Utilisateur non trouvée');
            return $this->redirectToRoute('user_information');
        }

        return $this->render('user/user_information/index.html.twig', [
            'actual_route' => $actual_route,
            'form' => $form->createView()
        ]);
    }
}
