<?php
/**
 * Created by PhpStorm.
 * User: matthieuparis
 * Date: 12/02/2019
 * Time: 16:50
 */

namespace App\Service;


use App\Entity\Event;
use App\Entity\Location;
use App\Entity\User;

class MailerService
{
    private $mailer;
    private $templates;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templates)
    {
        $this->mailer = $mailer;
        $this->templates = $templates;
    }

    public function sendMailVehicleLocation(Location $location)
    {
        $message = (new \Swift_Message("Vous n'avez pas rendu votre véhicule !"))
            ->setFrom('admin@e-motion.com')
            ->setTo($location->getUser()->getEmail())
            ->setBody(
                $this->templates->render(
                // templates/emails/registration.html.twig
                    'emails/index.html.twig',
                    ['user' => $location->getUser(),
                        'vehicle' => $location->getVehicle(),
                        'title' => "Vous n'avez pas rendu votre véhicule !"]
                ),
                'text/html'
            )/*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);
    }

    public function sendMailRegistration(User $user)
    {
        $message = (new \Swift_Message("Bienvenue chez E-Motion !"))
            ->setFrom('admin@e-motion.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->templates->render(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['user' => $user,
                        'title' => "Bienvenue chez E-Motion !"]
                ),
                'text/html'
            )/*
             * If you also want to include a plaintext version of the message
            ->addPart(
                $this->renderView(
                    'emails/registration.txt.twig',
                    ['name' => $name]
                ),
                'text/plain'
            )
            */
        ;

        $this->mailer->send($message);
    }

}
