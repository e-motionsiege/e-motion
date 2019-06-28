<?php

namespace App\Controller;

use App\Event\UserRegisteredEvent;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use App\Form\RegisterUserType;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\LoginUserType;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger)
    {
        $user = new User();
        $form = $this->createForm(LoginUserType::class, $user);
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error){
            $this->addFlash('danger', 'Erreur dans votre email ou mot de passe');
            return $this->redirectToRoute('login');
        }

        return $this->render('security/login.html.twig', [
            'error' => $error ? $error->getMessage() : null,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login/success", name="login_success")
     */
    public function loginRedirect()
    {
        $user = $this->getUser();
        $role = $user->getRoles();
        if (in_array("ROLE_ADMIN", $role)) {
            $this->addFlash('success', 'Bienvenue '.$user->getEmail().' !');
            return $this->redirectToRoute('admin_vehicle');
        } elseif (in_array("ROLE_USER", $role)) {
            $this->addFlash('success', 'Bienvenue '.$user->getEmail().' !');
            return $this->redirectToRoute('profile');
        }else{
            $this->addFlash('success', 'Bienvenue '.$user->getEmail().' !');
            return $this->redirectToRoute('owner');
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(AuthenticationUtils $authenticationUtils, LoggerInterface $logger)
    {
        if($this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $this->get('session')->invalidate();
        }
        $this->addFlash('success', 'User disconnected!');
        $this->redirectToRoute('home');
    }
}
