<?php

namespace App\Controller\Lk;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lk")
 */
class UserAddressController extends AbstractController
{
    /**
     * @Route("/", name="lk_user_address_list", methods={"GET"})
     *
     * @return Response
     */
    public function listAction(): Response
    {
        return $this->render('lk/user_address/list.html.twig');
    }
}
