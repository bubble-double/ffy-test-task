<?php

namespace App\Service\Lk;

use App\Entity\User;
use App\Service\DeliveryAddress\DeliveryAddressService;

class LkService
{
    /**
     * @var DeliveryAddressService
     */
    protected $deliveryAddressService;

    /**
     * @param DeliveryAddressService $deliveryAddressService
     */
    public function __construct(DeliveryAddressService $deliveryAddressService)
    {
        $this->deliveryAddressService = $deliveryAddressService;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getDeliveryAddressData(User $user): array
    {
        [$deliveryAddresses, $canAddNew] = $this->deliveryAddressService->getAll($user);
        return [
            'deliveryAddressesData' => [
                'deliveryAddresses' => $deliveryAddresses,
                'canAddNew' => $canAddNew,
            ],
        ];
    }
}
