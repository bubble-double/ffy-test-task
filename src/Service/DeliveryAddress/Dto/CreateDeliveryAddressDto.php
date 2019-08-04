<?php

namespace App\Service\DeliveryAddress\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateDeliveryAddressDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80
     * )
     *
     * @var string
     */
    protected $country;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80
     * )
     *
     * @var string
     */
    protected $city;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     *
     * @var string
     */
    protected $street;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      min = 1,
     *      max = 80
     * )
     *
     * @var string
     */
    protected $postcode;

    /**
     * @param string $country
     * @param string $city
     * @param string $street
     * @param string $postcode
     */
    public function __construct(string $country, string $city, string $street, string $postcode)
    {
        $this->country = $country;
        $this->city = $city;
        $this->street = $street;
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }
}
