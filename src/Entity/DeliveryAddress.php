<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="DeliveryAddress")
 * @ORM\Entity(repositoryClass="App\Repository\DeliveryAddressRepository")
 */
class DeliveryAddress
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
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(name="userId", referencedColumnName="id")
     *
     * @var User|null
     */
    protected $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80,
     *      minMessage = "Your country must be at least {{ limit }} characters long",
     *      maxMessage = "Your country cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=80)
     *
     * @var string
     */
    protected $country;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80,
     *      minMessage = "Your city must be at least {{ limit }} characters long",
     *      maxMessage = "Your city cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=80)
     *
     * @var string
     */
    protected $city;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 255,
     *      minMessage = "Your street must be at least {{ limit }} characters long",
     *      maxMessage = "Your street cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    protected $street;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80,
     *      minMessage = "Your postcode must be at least {{ limit }} characters long",
     *      maxMessage = "Your postcode cannot be longer than {{ limit }} characters"
     * )
     *
     * @ORM\Column(type="string", length=80)
     *
     * @var string
     */
    protected $postcode;

    /**
     * @Assert\Type("boolean")
     *
     * @ORM\Column(type="boolean", name="isDefault", options={"default" : 0})
     *
     * @var bool
     */
    protected $isDefault;

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
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(User $user = null): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     *
     * @return void
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return void
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     *
     * @return void
     */
    public function setStreet(string $street): void
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return $this->isDefault;
    }

    /**
     * @param bool $isDefault
     */
    public function setAsDefault(bool $isDefault): void
    {
        $this->isDefault = $isDefault;
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
}
