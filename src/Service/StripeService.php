<?php


namespace App\Service;


use App\Entity\Bill;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Charge;
use Stripe\Stripe;

class StripeService
{
    private $entityManager;

    /**
     * StripeService constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function authentication()
    {
        Stripe::setApiKey(getenv("STRIPE_SECRET_KEY"));
        return true;
    }

    public function charge(Location $location){
        $offer = $location->getOffer();
        $user = $location->getUser();
        $km = $location->getReturnKm();
        $startAt = $location->getStartAt()->format('Y-m-d');
        $endAt = $location->getEndAt()->format('Y-m-d');
        $returnAt = $location->getReturnAt()->format('Y-m-d');
        $end = strtotime($endAt);
        $start = strtotime($startAt);
        $return = strtotime($returnAt);
        $datediff = $return - $end;

        $day = (int)round($datediff / (60 * 60 * 24));

        if ($day > 0){
            $penality = 1.15;
        }else{
            $penality = 0;
        }
        $diffStartEnd = $end - $start;
        $totDay = (int)round($diffStartEnd / (60 * 60 * 24));

        $amount = (($km*$offer->getAmountKm())+($totDay*$offer->getAmountDuration()));

        $bill = new Bill();
        $bill->setName('Bill_Location_'.$location->getStartAt()->format('dmy').'_'.$location->getEndAt()->format('dmy'));
        $bill->setCreatedAt(new \DateTime());
        $bill->setAmount($amount);
        $bill->setPenality($penality);
        $bill->setLocation($location);
        $this->entityManager->persist($bill);

        if ($penality != 0){
            $charge = Charge::create([
                "amount" => (round(($amount*$penality),2)*100),
                "currency" => "eur",
                "source" => "tok_mastercard",
                "metadata" => ["order_id" => "6735"]
            ]);
        }else{
            $charge = Charge::create([
                "amount" => (round($amount,2)*100),
                "currency" => "eur",
                "source" => "tok_mastercard",
                "metadata" => ["order_id" => "6735"]
            ]);
        }
        return $charge;
    }
}
