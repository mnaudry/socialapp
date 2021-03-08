<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

    /**
      * Require ROLE_ADMIN for *every* controller method in this class.
      *
      * @IsGranted("ROLE_USER")
      */

class DashboardController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function home(): Response
    {

        return $this->render('dashboard/home.html.twig', [
             //'error' => $error
            ]);
    }
}
