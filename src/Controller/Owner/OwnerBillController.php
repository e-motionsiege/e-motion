<?php

namespace App\Controller\Owner;

use App\Repository\BillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OwnerBillController extends AbstractController
{
    /**
     * @Route("/owner/bill", name="owner_bill")
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
}
