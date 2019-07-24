<?php

namespace App\Controller\User;

use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BillController extends AbstractController
{

    private $htmltopdf;

    /**
     * BillController constructor.
     * @param $htmltopdf
     */
    public function __construct(\App\Service\HTML2PDF $htmltopdf)
    {
        $this->htmltopdf = $htmltopdf;
    }

    /**
     * @Route("/user/bill/{id}", name="bill")
     */

    public function index($id)
    {
        $html = $this->renderView('bill/index.html.twig', [
            'controller_name' => 'BillController',
        ]);

        $this->htmltopdf->create('P','A4','fr',true,'UTF-8',array(10,15,10,15));

        return $this->htmltopdf->generatePdf($html,'bill');
    }

}
