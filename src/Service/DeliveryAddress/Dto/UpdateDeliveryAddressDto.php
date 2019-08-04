<?php

namespace App\Service\DeliveryAddress\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateDeliveryAddressDto
{
    /**
     * @Assert\Length(max = 80)
     *
     * @var string|null
     */
    protected $country;

    /**
     * @Assert\Length(max = 80)
     *
     * @var string|null
     */
    protected $city;

    /**
     * @Assert\Length(max = 255)
     *
     * @var string|null
     */
    protected $street;

    /**
     * @Assert\Length(max = 80)
     *
     * @var string|null
     */
    protected $postcode;

    /**
     * @param string|null $country
     * @param string|null $city
     * @param string|null $street
     * @param string|null $postcode
     */
    public function __construct(
        string $country = null,
        string $city = null,
        string $street = null,
        string $postcode = null
    ) {
        $this->country = $country;
        $this->city = $city;
        $this->street = $street;
        $this->postcode = $postcode;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string
    {
        return $this->street;
    }

    /**
     * @return string|null
     */
    public function getPostcode(): ?string
    {
        return $this->postcode;
    }
}
