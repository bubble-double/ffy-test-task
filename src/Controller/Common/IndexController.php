<?php

namespace App\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="homepage", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        return $this->render('common/index.html.twig');
    }

    /**
     * @Route("/api-doc", name="api_doc", methods={"GET"})
     *
     * @return Response
     */
    public function apiDocAction(): Response
    {
        return $this->render('common/api_doc.html.twig');
    }
}
