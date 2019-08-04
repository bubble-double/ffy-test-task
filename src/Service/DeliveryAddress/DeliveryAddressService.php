<?php

namespace App\Service\DeliveryAddress;

use App\Entity\DeliveryAddress;
use App\Entity\User;
use App\Enum\DeliveryAddress\DeliveryAddressEnum;
use App\Repository\DeliveryAddressRepository;
use App\Service\DeliveryAddress\Dto\CreateDeliveryAddressDto;
use App\Service\DeliveryAddress\Dto\UpdateDeliveryAddressDto;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeliveryAddressService
{
    /**
     * @var DeliveryAddressRepository
     */
    protected $deliveryAddressRepository;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param DeliveryAddressRepository $deliveryAddressRepository
     * @param ValidatorInterface $validator
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        DeliveryAddressRepository $deliveryAddressRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ) {
        $this->deliveryAddressRepository = $deliveryAddressRepository;
        $this->validator = $validator;
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     *
     * @return DeliveryAddress[]
     */
    public function getAll(User $user): array
    {
        $deliveryAddresses = $this->deliveryAddressRepository->findBy(['user' => $user]);
        $canAddNew = DeliveryAddressEnum::MAX_COUNT > \count($deliveryAddresses);
        return [$deliveryAddresses, $canAddNew];
    }

    /**
     * @param int $deliveryAddressId
     * @param User $user
     *
     * @return DeliveryAddress
     *
     * @throws NotFoundHttpException
     */
    public function getOne(int $deliveryAddressId, User $user): DeliveryAddress
    {
        return $this->findOneOrFall($deliveryAddressId, $user);
    }

    /**
     * @param CreateDeliveryAddressDto $dto
     * @param User $user
     *
     * @return DeliveryAddress
     *
     * @throws ORMException
     * @throws ConnectionException
     * @throws ValidatorException
     * @throws \RuntimeException
     */
    public function create(CreateDeliveryAddressDto $dto, User $user): DeliveryAddress
    {
        $this->validateDto($dto);

        $countExisting = $this->deliveryAddressRepository->count(['user' => $user]);
        if (DeliveryAddressEnum::MAX_COUNT <= $countExisting) {
            throw new \RuntimeException(
                sprintf(
                    'Can`t add new delivery request. Count delivery requests already maximum and equal %s',
                    $countExisting
                )
            );
        }

        $this->entityManager->getConnection()->beginTransaction();
        try {
            // drop isDefault for existing
            $this->deliveryAddressRepository->updateAllDropPropertyIsDefault($user);

            $entity = new DeliveryAddress();
            $entity->setCountry($dto->getCountry());
            $entity->setCity($dto->getCity());
            $entity->setStreet($dto->getStreet());
            $entity->setPostcode($dto->getPostcode());
            $entity->setAsDefault(true);
            $entity->setUser($user);
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();

            return $entity;
        } catch (\Throwable $t) {
            $this->entityManager->getConnection()->rollBack();
            // @todo log it, send metric ...
            throw new ORMException('Can`t save delivery address');
        }
    }

    /**
     * @param UpdateDeliveryAddressDto $dto
     * @param int $deliveryAddressId
     * @param User $user
     *
     * @return DeliveryAddress
     *
     * @throws ValidatorException
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     */
    public function update(UpdateDeliveryAddressDto $dto, int $deliveryAddressId, User $user): DeliveryAddress
    {
        $this->validateDto($dto);

        $entity = $this->findOneOrFall($deliveryAddressId, $user);
        if ($dto->getCountry()) {
            $entity->setCountry($dto->getCountry());
        }

        if ($dto->getCity()) {
            $entity->setCity($dto->getCity());
        }

        if ($dto->getStreet()) {
            $entity->setStreet($dto->getStreet());
        }

        if ($dto->getPostcode()) {
            $entity->setPostcode($dto->getPostcode());
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @param int $deliveryAddressId
     * @param User $user
     *
     * @return DeliveryAddress
     *
     * @throws ORMException
     * @throws ConnectionException
     * @throws NotFoundHttpException
     */
    public function setAsDefault(int $deliveryAddressId, User $user): DeliveryAddress
    {
        $entity = $this->findOneOrFall($deliveryAddressId, $user);
        if (!$entity->isDefault()) {
            $this->entityManager->getConnection()->beginTransaction();
            try {
                // drop isDefault for existing
                $this->deliveryAddressRepository->updateAllDropPropertyIsDefault($user);

                $entity->setAsDefault(true);

                $this->entityManager->persist($entity);
                $this->entityManager->flush();
                $this->entityManager->getConnection()->commit();
            } catch (\Throwable $t) {
                $this->entityManager->getConnection()->rollBack();
                // @todo log it, send metric ...
                throw new ORMException('Can`t save delivery address');
            }
        }

        return $entity;
    }

    /**
     * @param int $deliveryAddressId
     * @param User $user
     *
     * @return void
     *
     * @throws NotFoundHttpException
     * @throws \RuntimeException
     */
    public function delete(int $deliveryAddressId, User $user): void
    {
        $entity = $this->findOneOrFall($deliveryAddressId, $user);
        if ($entity->isDefault()) {
            throw new \RuntimeException(
                sprintf(
                    'Can`t delete delivery entity because it set as default. DeliveryAddressId: %s',
                    $entity->getId()
                )
            );
        }

        $countExisting = $this->deliveryAddressRepository->count(['user' => $user]);
        if (1 === $countExisting) {
            throw new \RuntimeException(
                sprintf(
                    'Can`t delete delivery entity because it is once. DeliveryAddressId: %s',
                    $entity->getId()
                )
            );
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * @param mixed $dto
     *
     * @return void
     *
     * @throws ValidatorException
     * @throws \RuntimeException
     */
    protected function validateDto($dto): void
    {
        if (!is_object($dto) && !is_array($dto)) {
            throw new \RuntimeException('Was transferred param of unsupported type');
        }
        $violationList = $this->validator->validate($dto);
        if ($violationList->count()) {
            throw new ValidatorException($violationList);
        }
    }

    /**
     * @param int $deliveryAddressId
     * @param User $user
     *
     * @return DeliveryAddress
     *
     * @throws NotFoundHttpException
     */
    protected function findOneOrFall(int $deliveryAddressId, User $user): DeliveryAddress
    {
        $entity = $this->deliveryAddressRepository->findOneBy(['id' => $deliveryAddressId, 'user' => $user]);
        if (!$entity) {
            throw new NotFoundHttpException(
                sprintf(
                    'Delivery address was not found or does not belong to user. DeliveryAddressId: %s',
                    $deliveryAddressId
                )
            );
        }
        return $entity;
    }
}
