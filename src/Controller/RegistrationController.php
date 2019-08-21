<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, MailerService $mailerService, UserRepository $userRepository): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notValidPseudo = $userRepository->findOneBy(array('username'=>$user->getUsername()));
            if ($notValidPseudo){
                $this->addFlash('danger', 'Ce pseudo est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }
            $notValidEmail = $userRepository->findOneBy(array('email'=>$user->getEmail()));
            if ($notValidEmail){
                $this->addFlash('danger', 'Cet email est déjà utilisé.');
                return $this->redirectToRoute('app_register');
            }
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            // create user point line set to 0
            $point = new Point();
            $point->setUser($user);
            $point->setValue(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->persist($point);
            $entityManager->flush();

            $mailerService->sendMailRegistration($user);

            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
