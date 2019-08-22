<?php

namespace App\Controller\User;

use App\Repository\BillRepository;
use Spipu\Html2Pdf\Html2Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
/**
 * @Security("has_role('ROLE_USER') or has_role('ROLE_OWNER') or has_role('ROLE_PROPRIETAIRE')")
 */
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
     * @Route("/user/bill", name="owner_bill")
     */
    public function index(Request $request, BillRepository $billRepository)
    {
        $user = $this->getUser();
        $actual_route = $request->get('actual_route', 'owner_bill');
        $allBills = $billRepository->findBillsOwner($user);
        return $this->render('owner/owner_bill/index.html.twig', [
            'actual_route'=>$actual_route,
            'allBills'=>$allBills
        ]);
    }

    /**
     * @Route("/user/bill/{id}", name="bill")
     */

    public function getBill($id, BillRepository $billRepository)
    {
        $bill = $billRepository->findOneBy(['id'=>$id]);
        $html = $this->renderView('bill/index.html.twig', [
            'controller_name' => 'BillController',
            'invoice'=>$bill,
            'location'=>$bill->getLocation(),
            'user'=>$bill->getLocation()->getUser(),
            'vehicule'=>$bill->getLocation()->getVehicle(),
            'offer'=>$bill->getLocation()->getOffer()

        ]);

        $this->htmltopdf->create('P','A4','fr',true,'UTF-8',array(10,15,10,15));

        return $this->htmltopdf->generatePdf($html,'bill');
    }

}
