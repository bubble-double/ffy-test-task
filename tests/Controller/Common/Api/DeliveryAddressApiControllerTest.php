<?php


namespace App\Tests\Controller\Common\Api;

use App\Entity\DeliveryAddress;
use App\Entity\User;
use App\Enum\DeliveryAddress\DeliveryAddressEnum;
use App\Enum\Serializer\FormatTypeEnum;
use App\Repository\DeliveryAddressRepository;
use App\Service\DeliveryAddress\DeliveryAddressService;
use App\Service\DeliveryAddress\Dto\CreateDeliveryAddressDto;
use App\Service\DeliveryAddress\Dto\UpdateDeliveryAddressDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeliveryAddressApiControllerTest extends WebTestCase
{
    /**
     * @var string
     */
    protected const JSON_CREATE_ENTITY = '{"country":"Iceland","city":"Reykjav\u00edk","street":"Laugavegur","postcode":"AB-123987"}';

    /**
     * @var string
     */
    protected const JSON_UPDATE_ENTITY = '{"city":"Reykjav\u00edk_","street":"Laugavegur_"}';

    /**
     * @var ContainerInterface
     */
    protected $di;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var DeliveryAddressService
     */
    protected $deliveryAddressService;

    /**
     * @var DeliveryAddressRepository|MockObject
     */
    protected $mockDeliveryAddressRepository;

    /**
     * @var EntityManagerInterface|MockObject
     */
    protected $mockEntityManager;

    /**
     * @inheritDoc
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::bootKernel();

        $this->di = self::$kernel->getContainer();
        $this->validator = $this->di->get('validator');
        $this->serializer = $this->di->get('serializer');
        $this->mockEntityManager = $this->mockEntityManager();
    }

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->mockDeliveryAddressRepository = $this->mockDeliveryAddressRepository();
        $this->deliveryAddressService = new DeliveryAddressService(
            $this->mockDeliveryAddressRepository,
            $this->validator,
            $this->mockEntityManager
        );
    }

    /**
     * @var void
     */
    public function testGetAll(): void
    {
        $user = $this->mockUser();

        $mockDeliveryAddress = $this->mockDeliveryAddress();
        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findBy')
            ->willReturn([$mockDeliveryAddress]);

        /** @var DeliveryAddress[] $deliveryAddresses */
        [$deliveryAddresses, $canAddNew] = $this->deliveryAddressService->getAll($user);
        self::assertEquals(1, \count($deliveryAddresses));
        self::assertEquals(true, $canAddNew);
    }

    /**
     * @var void
     */
    public function testGetAllExistMaximumEntities(): void
    {
        $user = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress();
        $deliveryCollection = \array_fill(0, DeliveryAddressEnum::MAX_COUNT, $mockDeliveryAddress);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findBy')
            ->willReturn($deliveryCollection);

        /** @var bool $canAddNew */
        [$deliveryAddresses, $canAddNew] = $this->deliveryAddressService->getAll($user);
        self::assertCount(DeliveryAddressEnum::MAX_COUNT, $deliveryAddresses);
        self::assertFalse($canAddNew);
    }

    /**
     * @return void
     */
    public function testGetOne(): void
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($mockDeliveryAddress);

        $deliveryAddress = $this->deliveryAddressService->getOne($mockDeliveryAddress->getId(), $mockUser);
        self::assertInstanceOf(DeliveryAddress::class, $deliveryAddress);
    }

    /**
     * @return void
     */
    public function testGetOneObjectWasNotFound(): void
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // method trow exception
        $this->expectException(NotFoundHttpException::class);

        $deliveryAddress = $this->deliveryAddressService->getOne($mockDeliveryAddress->getId(), $mockUser);
        self::assertNull($deliveryAddress);
    }

    /**
     * @return void
     *
     * @throws ConnectionException
     * @throws ORMException
     */
    public function testCreate(): void
    {
        /** @var CreateDeliveryAddressDto $dto */
        $dto = $this->serializer->deserialize(
            self::JSON_CREATE_ENTITY,
            CreateDeliveryAddressDto::class,
            FormatTypeEnum::JSON
        );

        $mockUser = $this->mockUser();
        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('count')
            ->willReturn(DeliveryAddressEnum::MAX_COUNT - 1);

        $deliveryAddress = $this->deliveryAddressService->create($dto, $mockUser);
        self::assertInstanceOf(DeliveryAddress::class, $deliveryAddress);
    }

    /**
     * @return void
     *
     * @throws ConnectionException
     * @throws ORMException
     */
    public function testCreateWhenExistMaximumEntities(): void
    {
        /** @var CreateDeliveryAddressDto $dto */
        $dto = $this->serializer->deserialize(
            self::JSON_CREATE_ENTITY,
            CreateDeliveryAddressDto::class,
            FormatTypeEnum::JSON
        );

        $mockUser = $this->mockUser();
        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('count')
            ->willReturn(DeliveryAddressEnum::MAX_COUNT);

        // method trow exception
        $this->expectException(\RuntimeException::class);

        $deliveryAddress = $this->deliveryAddressService->create($dto, $mockUser);
        self::assertNull($deliveryAddress);
    }

    /**
     * @return void
     */
    public function testUpdate(): void
    {
        /** @var UpdateDeliveryAddressDto $dto */
        $dto = $this->serializer->deserialize(
            self::JSON_UPDATE_ENTITY,
            UpdateDeliveryAddressDto::class,
            FormatTypeEnum::JSON
        );

        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($mockDeliveryAddress);

        $deliveryAddress = $this->deliveryAddressService->update($dto, $mockDeliveryAddress->getId(), $mockUser);
        self::assertEquals($deliveryAddress->getCity(), $dto->getCity());
        self::assertEquals($deliveryAddress->getStreet(), $dto->getStreet());
    }

    /**
     * @return void
     */
    public function testUpdateObjectWasNotFound(): void
    {
        /** @var UpdateDeliveryAddressDto $dto */
        $dto = $this->serializer->deserialize(
            self::JSON_UPDATE_ENTITY,
            UpdateDeliveryAddressDto::class,
            FormatTypeEnum::JSON
        );

        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // method trow exception
        $this->expectException(NotFoundHttpException::class);
        $deliveryAddress = $this->deliveryAddressService->update($dto, $mockDeliveryAddress->getId(), $mockUser);
        self::assertEquals($deliveryAddress, $mockDeliveryAddress);
    }

    /**
     * @return void
     *
     * @throws ConnectionException
     * @throws ORMException
     */
    public function testSetAsDefault(): void
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($mockDeliveryAddress);

        self::assertFalse($mockDeliveryAddress->isDefault());
        $deliveryAddress = $this->deliveryAddressService->setAsDefault($mockDeliveryAddress->getId(), $mockUser);
        self::assertTrue($deliveryAddress->isDefault());
    }

    /**
     * @return void
     *
     * @throws ConnectionException
     * @throws ORMException
     */
    public function testSetAsDefaultObjectWasNotFound(): void
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // method trow exception
        $this->expectException(NotFoundHttpException::class);

        $deliveryAddress = $this->deliveryAddressService->setAsDefault($mockDeliveryAddress->getId(), $mockUser);
        self::assertNull($deliveryAddress);
    }

    /**
     * @return void
     */
    public function testDeleteObjectWasNotFound()
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);

        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        // method trow exception
        $this->expectException(NotFoundHttpException::class);

        $this->deliveryAddressService->delete($mockDeliveryAddress->getId(), $mockUser);
    }

    /**
     * @return void
     */
    public function testDeleteWhenAddressSetAsDefault(): void
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser, 'isDefault' => true]);
        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($mockDeliveryAddress);

        // method trow exception
        $this->expectException(\RuntimeException::class);

        $this->deliveryAddressService->delete($mockDeliveryAddress->getId(), $mockUser);
    }

    /**
     * @return void
     */
    public function testDeleteWhenExistOneAddress(): void
    {
        $mockUser = $this->mockUser();
        $mockDeliveryAddress = $this->mockDeliveryAddress(['user' => $mockUser]);
        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn($mockDeliveryAddress);
        $this->mockDeliveryAddressRepository->expects($this->once())
            ->method('count')
            ->willReturn(1);

        // method trow exception
        $this->expectException(\RuntimeException::class);

        $this->deliveryAddressService->delete($mockDeliveryAddress->getId(), $mockUser);
    }

    /**
     * @param array $data
     *
     * @return User|MockObject
     */
    protected function mockUser(array $data = []): MockObject
    {
        /** @var User|MockObject $mockUsers */
        $mockUsers = $this->getMockBuilder(User::class)
            ->setMethods(['getId'])
            ->getMock();
        $mockUsers->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($data['id'] ?? 1));

        $mockUsers->setUsername($data['username'] ?? 'user1');
        $mockUsers->setEmail($data['email'] ?? 'user1@user1.com');

        return $mockUsers;
    }

    /**
     * @param array $data
     *
     * @return MockObject|DeliveryAddress
     */
    protected function mockDeliveryAddress(array $data = []): MockObject
    {
        /** @var DeliveryAddress|MockObject $mockDeliveryAddress */
        $mockDeliveryAddress = $this->getMockBuilder(DeliveryAddress::class)
            ->setMethods(['getId'])
            ->getMock();
        $mockDeliveryAddress->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($data['id'] ?? 1));

        $mockDeliveryAddress->setUser($data['user'] ?? $this->mockUser());
        $mockDeliveryAddress->setCountry($data['country'] ?? 'country1');
        $mockDeliveryAddress->setCity($data['city'] ?? 'city1');
        $mockDeliveryAddress->setStreet($data['street'] ?? 'street1');
        $mockDeliveryAddress->setPostcode($data['postcode'] ?? 'postcode1');
        $mockDeliveryAddress->setAsDefault($data['isDefault'] ?? false);

        return $mockDeliveryAddress;
    }

    /**
     * @return MockObject|DeliveryAddressRepository
     */
    protected function mockDeliveryAddressRepository(): MockObject
    {
        $deliveryAddressRepository = $this->getMockBuilder(DeliveryAddressRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $deliveryAddressRepository;
    }

    /**
     * @return MockObject|EntityManagerInterface
     */
    protected function mockEntityManager(): MockObject
    {
        $mockConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockEntityManager = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockEntityManager->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        return $mockEntityManager;
    }
}