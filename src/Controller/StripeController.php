<?php

namespace App\Controller;

use Stripe\Customer;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/stripe", name="stripe")
     */

    public function __construct()
    {
    }

    /**
     * @Route("/stripe/auth", name="stripe_auth")
     */
    public function authentication()
    {
        Stripe::setApiKey(getenv("STRIPE_SECRET_KEY"));
        $customer = Customer::create();
        dd($customer->getLastResponse());
        return true;
    }
}
