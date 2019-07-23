<?php

namespace App\Controller;

use App\Entity\Vehicle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Entity\User;

use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $vehicules = $em->getRepository(Vehicle::class)->findAll();

        return $this->render('home/index.html.twig', [
            'vehicules' => $vehicules
        ]);
    }

}
