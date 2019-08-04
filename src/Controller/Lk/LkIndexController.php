<?php

namespace App\Controller\Lk;

use App\Service\Lk\LkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lk")
 */
class LkIndexController extends AbstractController
{
    /**
     * @var LkService
     */
    protected $lkService;

    /**
     * @param LkService $lkService
     */
    public function __construct(LkService $lkService)
    {
        $this->lkService = $lkService;
    }

    /**
     * @Route("/", name="lk_index", methods={"GET"})
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $deliveryAddressesData = $this->lkService->getDeliveryAddressData($this->getUser());
        return $this->render(
            'lk/index.html.twig',
            [
                'deliveryAddressesData' => $deliveryAddressesData['deliveryAddressesData'] ?? [],
            ]
        );
    }
}
