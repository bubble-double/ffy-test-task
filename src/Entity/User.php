<?php

namespace App\Entity;

use App\Enum\User\UserRolesEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="User")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DeliveryAddress", mappedBy="user", orphanRemoval=true)
     *
     * @var Collection|DeliveryAddress[]
     */
    protected $deliveryAddresses;

    /**
     * @Assert\Email()
     * @Assert\Length(
     *      min = 1,
     *      max = 80,
     *      minMessage = "Your email must be at least {{ limit }} characters long",
     *      maxMessage = "Your email cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=80)
     *
     * @var string
     */
    protected $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $password;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=80)
     *
     * @var string
     */
    protected $username;

    /**
     * @Assert\Length(
     *      max = 80,
     *      maxMessage = "Your lastName cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=80, nullable=true)
     *
     * @var string|null
     */
    protected $lastName;

    /**
     * @Assert\Length(
     *      max = 255,
     *      maxMessage = "Your salt cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    protected $salt;

    /**
     * @Assert\Length(
     *      max = 512,
     *      maxMessage = "Your roles cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=512, nullable=true)
     *
     * @var string|null
     */
    protected $roles;

    /**
     * @Assert\DateTime()
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="create")
     *
     * @var \DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Gedmo\Mapping\Annotation\Timestampable(on="update")
     *
     * @var \DateTime|null
     */
    protected $updated;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->updated = new \DateTime();
        $this->deliveryAddresses = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection|DeliveryAddress[]
     */
    public function getDeliveryAddresses(): Collection
    {
        return $this->deliveryAddresses;
    }

    /**
     * @param DeliveryAddress $deliveryAddress
     *
     * @return void
     */
    public function addDeliveryAddress(DeliveryAddress $deliveryAddress): void
    {
        if (!$this->deliveryAddresses->contains($deliveryAddress)) {
            $this->deliveryAddresses[] = $deliveryAddress;
            $deliveryAddress->setUser($this);
        }
    }

    /**
     * @param DeliveryAddress $deliveryAddress
     *
     * @return void
     */
    public function removeDeliveryAddress(DeliveryAddress $deliveryAddress): void
    {
        if ($this->deliveryAddresses->contains($deliveryAddress)) {
            $this->deliveryAddresses->removeElement($deliveryAddress);
            // set the owning side to null (unless already changed)
            if ($deliveryAddress->getUser() === $this) {
                $deliveryAddress->setUser(null);
            }
        }
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @inheritdoc
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     */
    public function setUsername(string $username = null): void
    {
        $this->username = $username;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     */
    public function setLastName(string $lastName = null): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string|null
     */
    public function getSalt(): ?string
    {
        return $this->salt;
    }

    /**
     * @param string|null $salt
     */
    public function setSalt(string $salt = null): void
    {
        $this->salt = $salt;
    }

    /**
     * @inheritdoc
     */
    public function getRoles(): array
    {
        // @todo test task
        return [UserRolesEnum::ROLE_USER];
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created): void
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime|null $updated
     */
    public function setUpdated(\DateTime $updated = null): void
    {
        $this->updated = $updated;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials(): void
    {}
}
