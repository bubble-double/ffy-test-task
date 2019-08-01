<?php

namespace App\Controller\Lk;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lk")
 */
class LkIndexController extends AbstractController
{
    /**
     * @Route("/", name="lk_index", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('lk/index.html.twig');
    }
}
