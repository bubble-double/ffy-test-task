<?php

namespace App\Controller\Common\Api;

use App\Enum\Serializer\FormatTypeEnum;
use App\Service\DeliveryAddress\DeliveryAddressService;
use App\Service\DeliveryAddress\Dto\CreateDeliveryAddressDto;
use App\Service\DeliveryAddress\Dto\UpdateDeliveryAddressDto;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * @Route("/api/delivery-address")
 */
class DeliveryAddressApiController extends AbstractApiController
{
    /**
     * @var DeliveryAddressService
     */
    protected $deliveryAddressService;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param DeliveryAddressService $deliveryAddressService
     * @param SerializerInterface $serializer
     */
    public function __construct(DeliveryAddressService $deliveryAddressService, SerializerInterface $serializer)
    {
        $this->deliveryAddressService = $deliveryAddressService;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/{deliveryAddressId<\d+>}", name="api_delivery_address_get", methods={"GET"})
     *
     * @param int $deliveryAddressId
     *
     * @return JsonResponse
     *
     * @throws NotFoundHttpException
     */
    public function getAction(int $deliveryAddressId): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $deliveryAddress = $this->deliveryAddressService->getOne($deliveryAddressId, $this->getUser());
        return $this->sendApiMessage(['deliveryAddress' => $deliveryAddress]);
    }

    /**
     * @Route("/", name="api_delivery_address_new", methods={"POST"})
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ORMException
     * @throws ValidatorException
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function newAction(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var CreateDeliveryAddressDto $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            CreateDeliveryAddressDto::class,
            FormatTypeEnum::JSON
        );

        $deliveryAddress = $this->deliveryAddressService->create($dto, $this->getUser());
        return $this->sendApiMessage(['deliveryAddress' => $deliveryAddress]);
    }

    /**
     * @Route("/{deliveryAddressId<\d+>}", name="api_delivery_address_update", methods={"PUT"})
     *
     * @param int $deliveryAddressId
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws ValidatorException
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     */
    public function updateAction(int $deliveryAddressId, Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var UpdateDeliveryAddressDto $dto */
        $dto = $this->serializer->deserialize(
            $request->getContent(),
            UpdateDeliveryAddressDto::class,
            FormatTypeEnum::JSON
        );

        $deliveryAddress = $this->deliveryAddressService->update($dto, $deliveryAddressId, $this->getUser());
        return $this->sendApiMessage(['deliveryAddress' => $deliveryAddress]);
    }

    /**
     * @Route("/{deliveryAddressId<\d+>}/set-as-default", name="api_delivery_address_set_default", methods={"PUT"})
     *
     * @param int $deliveryAddressId
     *
     * @return JsonResponse
     *
     * @throws ORMException
     * @throws ConnectionException
     * @throws NotFoundHttpException
     */
    public function setAsDefaultAction(int $deliveryAddressId): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $deliveryAddress = $this->deliveryAddressService->setAsDefault($deliveryAddressId, $this->getUser());
        return $this->sendApiMessage(['deliveryAddress' => $deliveryAddress]);
    }

    /**
     * @Route("/{deliveryAddressId<\d+>}", name="api_delivery_address_delete", methods={"DELETE"})
     *
     * @param int $deliveryAddressId
     *
     * @return JsonResponse
     *
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     */
    public function deleteAction(int $deliveryAddressId): JsonResponse
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $this->deliveryAddressService->delete($deliveryAddressId, $this->getUser());
        return $this->sendApiMessage(['deliveryAddress' => []]);
    }
}
