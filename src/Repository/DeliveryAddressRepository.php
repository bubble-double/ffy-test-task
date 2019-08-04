<?php

namespace App\Repository;

use App\Entity\DeliveryAddress;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DeliveryAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method DeliveryAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method DeliveryAddress[]    findAll()
 * @method DeliveryAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryAddressRepository extends ServiceEntityRepository
{
    /**
     * @inheritdoc
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DeliveryAddress::class);
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function updateAllDropPropertyIsDefault(User $user): int
    {
        $qb = $this->createQueryBuilder('da');
        return $qb->update()
            ->set('da.isDefault', $qb->expr()->literal(false))
            ->where($qb->expr()->eq('da.user', ':user'))
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->execute();
    }
}
